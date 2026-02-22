<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

/**
 * Service principal de l'assistant IA
 * Orchestre RAG et Ollama pour générer des réponses intelligentes
 */
class AIAssistantService
{
    private OllamaService $ollamaService;
    private RAGService $ragService;
    private LoggerInterface $logger;
    private ?\App\Service\ActionExecutorService $actionExecutor;

    public function __construct(
        OllamaService $ollamaService,
        RAGService $ragService,
        LoggerInterface $logger,
        ?\App\Service\ActionExecutorService $actionExecutor = null
    ) {
        $this->ollamaService = $ollamaService;
        $this->ragService = $ragService;
        $this->logger = $logger;
        $this->actionExecutor = $actionExecutor;
    }

    /**
     * Traite une question utilisateur et génère une réponse
     */
    public function ask(string $question, array $options = []): array
    {
        $startTime = microtime(true);

        try {
            // Vérifier si Ollama est disponible
            $ollamaAvailable = $this->ollamaService->isAvailable();
            
            // UTILISER FALLBACK PAR DÉFAUT (plus rapide et fiable)
            // Ollama est trop lent, on utilise le mode fallback intelligent
            if (!$ollamaAvailable || true) { // Force fallback
                return $this->getFallbackResponse($question);
            }

            // 1. Collecter le contexte via RAG
            $context = $this->ragService->retrieveContext($question);

            // 2. Générer la réponse avec Ollama
            $response = $this->ollamaService->generate($question, $context, $options);

            if (!$response) {
                return $this->getFallbackResponse($question);
            }

            // 2.5. Détecter et exécuter les actions si présentes
            $actionResult = $this->detectAndExecuteAction($response, $context);
            if ($actionResult) {
                $response = $actionResult;
            }

            // 3. Post-traiter la réponse
            $processedResponse = $this->postProcessResponse($response, $context);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'success' => true,
                'response' => $processedResponse,
                'context_used' => !empty($context['data']),
                'duration_ms' => $duration,
                'model' => $options['model'] ?? 'llama3.2:3b'
            ];

        } catch (\Exception $e) {
            $this->logger->error('AI Assistant error', [
                'question' => $question,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'response' => "Désolé, je rencontre un problème technique. Veuillez réessayer dans quelques instants.",
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Détecte et exécute une action dans la réponse de l'IA
     */
    private function detectAndExecuteAction(string $response, array $context): ?string
    {
        // Chercher le pattern ACTION:action_name|param1:value1|param2:value2
        if (!preg_match('/ACTION:([a-z_]+)(\|[^|]+)*/', $response, $matches)) {
            return null;
        }

        if (!$this->actionExecutor) {
            return null;
        }

        $actionString = $matches[0];
        $parts = explode('|', $actionString);
        $actionName = str_replace('ACTION:', '', $parts[0]);
        
        // Parser les paramètres
        $params = [];
        for ($i = 1; $i < count($parts); $i++) {
            if (strpos($parts[$i], ':') !== false) {
                list($key, $value) = explode(':', $parts[$i], 2);
                $params[$key] = $value;
            }
        }

        // Obtenir l'utilisateur depuis le contexte
        $user = $context['user'] ?? null;
        if (!$user) {
            return "❌ Impossible d'exécuter l'action: utilisateur non identifié.";
        }

        // Exécuter l'action
        try {
            $result = $this->actionExecutor->executeAction($actionName, $params, $user);
            
            if ($result['success']) {
                // Construire le message de résultat selon le type d'action
                $resultMessage = "";
                
                // Pour get_inactive_users
                if (isset($result['users']) && is_array($result['users'])) {
                    $count = $result['count'] ?? count($result['users']);
                    $resultMessage = "✅ {$count} utilisateur(s) inactif(s) trouvé(s)";
                    
                    if ($count > 0 && $count <= 5) {
                        $resultMessage .= ":\n";
                        foreach (array_slice($result['users'], 0, 5) as $u) {
                            $resultMessage .= "• {$u['prenom']} {$u['nom']} ({$u['email']}) - Dernière connexion: {$u['last_login']}\n";
                        }
                    }
                }
                // Pour create_student
                elseif (isset($result['user_id'])) {
                    $resultMessage = "✅ " . ($result['message'] ?? 'Étudiant créé avec succès');
                    $resultMessage .= "\n📋 ID: " . $result['user_id'];
                    if (isset($result['email'])) {
                        $resultMessage .= "\n📧 Email: " . $result['email'];
                    }
                    if (isset($result['default_password'])) {
                        $resultMessage .= "\n🔑 Mot de passe: " . $result['default_password'];
                    }
                }
                // Pour create_team
                elseif (isset($result['team_id'])) {
                    $resultMessage = "✅ " . ($result['message'] ?? 'Équipe créée avec succès');
                    $resultMessage .= "\n📋 ID: " . $result['team_id'];
                    if (isset($result['event'])) {
                        $resultMessage .= "\n🎯 Événement: " . $result['event'];
                    }
                }
                // Pour get_popular_courses
                elseif (isset($result['courses']) && is_array($result['courses'])) {
                    $count = $result['count'] ?? count($result['courses']);
                    $resultMessage = "✅ {$count} cours populaire(s):\n";
                    foreach (array_slice($result['courses'], 0, 5) as $c) {
                        $resultMessage .= "• {$c['titre']} ({$c['niveau']}, {$c['chapitres']} chapitres)\n";
                    }
                }
                // Message générique avec message
                elseif (isset($result['message'])) {
                    $resultMessage = "✅ " . $result['message'];
                }
                // Fallback
                else {
                    $resultMessage = "✅ Action exécutée avec succès";
                }
                
                return str_replace($actionString, $resultMessage, $response);
            } else {
                $errorMsg = $result['error'] ?? 'Erreur inconnue';
                return str_replace($actionString, "❌ " . $errorMsg, $response);
            }
        } catch (\Exception $e) {
            $this->logger->error('Action execution failed', [
                'action' => $actionName,
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            return str_replace($actionString, "❌ Erreur lors de l'exécution de l'action.", $response);
        }
    }

    /**
     * Post-traite la réponse pour ajouter des liens et actions
     */
    private function postProcessResponse(string $response, array $context): string
    {
        try {
            // Ajouter des liens vers les cours mentionnés
            if (isset($context['data']['available_courses']) && is_array($context['data']['available_courses'])) {
                foreach ($context['data']['available_courses'] as $cours) {
                    if (!is_array($cours) || !isset($cours['titre']) || !isset($cours['id'])) {
                        continue;
                    }
                    
                    $titre = $cours['titre'];
                    $id = $cours['id'];
                    // Remplacer les mentions de cours par des liens
                    $response = preg_replace(
                        '/\b' . preg_quote($titre, '/') . '\b/i',
                        "<a href='/cours/{$id}' class='ai-link'>{$titre}</a>",
                        $response,
                        1
                    );
                }
            }

            // Ajouter des liens vers les événements mentionnés
            if (isset($context['data']['upcoming_events']) && is_array($context['data']['upcoming_events'])) {
                foreach ($context['data']['upcoming_events'] as $event) {
                    if (!is_array($event) || !isset($event['titre']) || !isset($event['id'])) {
                        continue;
                    }
                    
                    $titre = $event['titre'];
                    $id = $event['id'];
                    $response = preg_replace(
                        '/\b' . preg_quote($titre, '/') . '\b/i',
                        "<a href='/events/{$id}' class='ai-link'>{$titre}</a>",
                        $response,
                        1
                    );
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Error in postProcessResponse', ['error' => $e->getMessage()]);
        }

        return $response;
    }

    /**
     * Réponse de secours si Ollama n'est pas disponible
     * Utilise RAG + détection d'actions pour des réponses intelligentes
     */
    private function getFallbackResponse(string $question): array
    {
        $questionLower = strtolower($question);

        try {
            // Essayer d'utiliser RAG pour obtenir des données réelles
            $context = $this->ragService->retrieveContext($question);
            $userName = $context['user_name'] ?? 'Invité';
            $userLevel = $context['user_level'] ?? 'DEBUTANT';
            $userRole = $context['user_role'] ?? 'GUEST';

            // DÉTECTION D'ACTIONS ADMIN (mode fallback intelligent)
            if ($userRole === 'ADMIN') {
                // Créer un étudiant
                if (preg_match('/(creer|créer|create|nouveau|new).*etudiant/i', $questionLower)) {
                    // Extraire nom, email
                    $nom = '';
                    $email = '';
                    
                    if (preg_match('/nom[:\s]+([a-z]+)/i', $question, $m)) {
                        $nom = $m[1];
                    }
                    if (preg_match('/email[:\s]+([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})/i', $question, $m)) {
                        $email = $m[1];
                    }
                    
                    if ($nom && $email) {
                        return [
                            'success' => true,
                            'response' => "ACTION:create_student|nom:{$nom}|prenom:{$nom}|email:{$email}|niveau:DEBUTANT",
                            'fallback' => true
                        ];
                    } else {
                        return [
                            'success' => true,
                            'response' => "Pour créer un étudiant, j'ai besoin du nom et de l'email. Exemple: creer etudiant nom:Rami email:rami@mail.com",
                            'fallback' => true
                        ];
                    }
                }
                
                // Suspendre un utilisateur
                if (preg_match('/(suspend|suspendre).*user/i', $questionLower)) {
                    if (preg_match('/user.*id[:\s]*(\d+)/i', $question, $m)) {
                        $userId = $m[1];
                        $reason = 'Suspendu par admin';
                        
                        if (preg_match('/reason[:\s]+([a-z]+)/i', $question, $m2)) {
                            $reason = $m2[1];
                        }
                        
                        return [
                            'success' => true,
                            'response' => "ACTION:suspend_user|user_id:{$userId}|reason:{$reason}",
                            'fallback' => true
                        ];
                    } else {
                        return [
                            'success' => true,
                            'response' => "Pour suspendre un utilisateur, j'ai besoin de l'ID. Exemple: suspend userid 10 reason inactif",
                            'fallback' => true
                        ];
                    }
                }
                
                // Utilisateurs inactifs
                if (preg_match('/(inactif|inactive|inactivity)/i', $questionLower)) {
                    $days = 7;
                    if (preg_match('/(\d+)\s*(jour|day)/i', $question, $m)) {
                        $days = $m[1];
                    }
                    
                    return [
                        'success' => true,
                        'response' => "ACTION:get_inactive_users|days:{$days}",
                        'fallback' => true
                    ];
                }
                
                // Statistiques
                if (preg_match('/(combien|statistique|stat|nombre).*utilisateur/i', $questionLower)) {
                    $stats = $context['data'] ?? [];
                    $total = $stats['total_users'] ?? 0;
                    $actifs = $stats['active_users'] ?? 0;
                    $suspendus = $stats['suspended_users'] ?? 0;
                    $inactifs = $stats['inactive_users_7days'] ?? 0;
                    
                    return [
                        'success' => true,
                        'response' => "📊 {$actifs} utilisateurs actifs sur {$total} au total. {$suspendus} suspendus, {$inactifs} inactifs depuis 7 jours.",
                        'fallback' => true
                    ];
                }
            }

            // Salutations
            if (preg_match('/(bonjour|salut|hello|hi|hey|bnjr|slt|مرحبا)/i', $questionLower)) {
                $response = "Salut! Comment je peux t'aider? 😊";
            }
            // Recommandation de cours avec filtrage intelligent par niveau ET par sujet
            elseif (preg_match('/(cours|apprendre|recommand|choisir|programming|étudier|douee|doué|bon|forte)/i', $questionLower)) {
                if (!empty($context['data']['recommended_courses'])) {
                    $recommendedCourses = $context['data']['recommended_courses'];
                    
                    // Détecter le sujet spécifique demandé
                    $sujetDemande = null;
                    if (preg_match('/python/i', $questionLower)) {
                        $sujetDemande = 'Python';
                    } elseif (preg_match('/java/i', $questionLower)) {
                        $sujetDemande = 'Java';
                    } elseif (preg_match('/web|javascript|react|html|css/i', $questionLower)) {
                        $sujetDemande = 'Web';
                    } elseif (preg_match('/ia|intelligence artificielle|machine learning|ml/i', $questionLower)) {
                        $sujetDemande = 'IA';
                    }
                    
                    // Filtrer par sujet si spécifié
                    $coursFiltres = $recommendedCourses;
                    if ($sujetDemande) {
                        $coursFiltres = array_filter($recommendedCourses, function($cours) use ($sujetDemande) {
                            $titre = strtolower($cours['titre']);
                            $matiere = strtolower($cours['matiere']);
                            $sujet = strtolower($sujetDemande);
                            return strpos($titre, $sujet) !== false || strpos($matiere, $sujet) !== false;
                        });
                    }
                    
                    $totalFiltres = count($coursFiltres);
                    
                    if ($totalFiltres > 0) {
                        if ($sujetDemande) {
                            $response = "🎓 **Cours {$sujetDemande} pour votre niveau {$userLevel}:**\n\n";
                        } else {
                            $response = "🎓 **Cours recommandés pour votre niveau {$userLevel}:**\n\n";
                        }
                        
                        // Afficher jusqu'à 3 cours
                        $count = 0;
                        foreach ($coursFiltres as $cours) {
                            if ($count >= 3) break;
                            $chapitres = $cours['chapitres_count'] > 0 ? $cours['chapitres_count'] . ' chapitres' : 'En préparation';
                            $response .= "• **{$cours['titre']}**\n";
                            $response .= "  Niveau: {$cours['niveau']} | Durée: {$cours['duree']}h | {$chapitres}\n";
                            if (!empty($cours['description'])) {
                                $desc = substr($cours['description'], 0, 80);
                                $response .= "  {$desc}...\n";
                            }
                            $response .= "\n";
                            $count++;
                        }
                        
                        if ($totalFiltres > 3) {
                            $response .= "... et " . ($totalFiltres - 3) . " autres cours {$sujetDemande}!\n\n";
                        }
                        
                        if ($sujetDemande) {
                            $response .= "💡 Vous êtes doué en {$sujetDemande}? Ces cours avancés sont parfaits pour vous!";
                        } else {
                            $response .= "💡 Ces cours correspondent à votre niveau {$userLevel}.";
                        }
                    } else {
                        if ($sujetDemande) {
                            $response = "🎓 Aucun cours {$sujetDemande} adapté à votre niveau {$userLevel} pour le moment.\n\n" .
                                       "Consultez le catalogue pour voir d'autres cours!";
                        } else {
                            $response = "🎓 Aucun cours adapté à votre niveau {$userLevel} pour le moment.\n\n" .
                                       "Consultez le catalogue pour voir tous les cours disponibles!";
                        }
                    }
                } else {
                    $response = "🎓 **Nos cours disponibles:**\n\n" .
                               "• **Python** - Idéal pour débuter en programmation\n" .
                               "• **Java** - Pour la programmation orientée objet\n" .
                               "• **Développement Web** - HTML, CSS, JavaScript\n\n" .
                               "💡 Consultez notre catalogue de cours pour plus de détails!";
                }
            }
            // Événements avec données réelles et équipes
            elseif (preg_match('/(événement|event|semaine|mois|particip|équipe|team|rejoindre|groupe)/i', $questionLower)) {
                if (!empty($context['data']['upcoming_events'])) {
                    $events = $context['data']['upcoming_events'];
                    
                    // Détecter si la question concerne les équipes
                    $questionEquipes = preg_match('/(équipe|team|rejoindre|groupe)/i', $questionLower);
                    
                    if ($questionEquipes && count($events) > 0) {
                        // Réponse sur les équipes
                        $event = $events[0]; // Premier événement
                        $response = "Pour {$event['titre']}: ";
                        
                        if (!empty($event['equipes'])) {
                            $nbEquipes = count($event['equipes']);
                            $response .= "{$nbEquipes} équipe" . ($nbEquipes > 1 ? 's' : '') . " (";
                            
                            $equipesDispos = array_filter($event['equipes'], function($e) {
                                return $e['peut_rejoindre'];
                            });
                            
                            if (count($equipesDispos) > 0) {
                                $response .= count($equipesDispos) . " peuvent encore recruter). ";
                            } else {
                                $response .= "toutes complètes). ";
                            }
                        } else {
                            $response .= "aucune équipe créée pour le moment. ";
                        }
                        
                        $response .= "Règle: 4-6 membres par équipe, 1 seule équipe par événement. 👥";
                    } else {
                        // Réponse sur les événements
                        $count = count($events);
                        $response = "{$count} événement" . ($count > 1 ? 's' : '') . ": ";
                        
                        $eventsList = [];
                        foreach (array_slice($events, 0, 2) as $event) {
                            $eventsList[] = "{$event['titre']} ({$event['date']}, {$event['places_disponibles']} places)";
                        }
                        $response .= implode(' et ', $eventsList) . ". 🎫";
                    }
                } else {
                    $response = "Aucun événement prévu pour le moment. Consulte régulièrement la page événements! 📅";
                }
            }
            // Statistiques utilisateur avec données réelles
            elseif (preg_match('/(progrès|statistique|activité|historique|mes cours)/i', $questionLower)) {
                if (!empty($context['data']['user_id'])) {
                    $stats = $context['data'];
                    $response = "Tu t'es connecté ";
                    
                    if (!empty($stats['recent_activities'])) {
                        $count = min(3, count($stats['recent_activities']));
                        $response .= "{$count} fois récemment: ";
                        
                        $activities = array_slice($stats['recent_activities'], 0, 3);
                        $dates = array_map(function($a) { return $a['date']; }, $activities);
                        $response .= implode(', ', $dates);
                        
                        $allSuccess = array_reduce($activities, function($carry, $a) {
                            return $carry && $a['success'];
                        }, true);
                        
                        if ($allSuccess) {
                            $response .= ". Toutes les connexions ont réussi. 💪";
                        }
                    } else {
                        $response .= "mais je n'ai pas d'historique détaillé. Consulte ton profil pour plus d'infos.";
                    }
                } else {
                    $response = "Consulte ton profil pour voir tes cours, quiz et activités. 📊";
                }
            }
            // Aide générale
            elseif (preg_match('/(aide|help|comment|progresser)/i', $questionLower)) {
                $response = "💡 **Je peux vous aider à:**\n\n" .
                           "• Trouver des cours adaptés à votre niveau ({$userLevel})\n" .
                           "• Découvrir les événements à venir\n" .
                           "• Suivre vos progrès\n" .
                           "• Naviguer sur la plateforme\n\n" .
                           "Posez-moi une question spécifique! 😊";
            }
            // Réponse par défaut - Plus de détection hors sujet
            else {
                $response = "Je peux t'aider avec les cours, événements, équipes et tes progrès. Pose-moi une question spécifique! 😊";
            }

            return [
                'success' => true,
                'response' => $response,
                'fallback' => true,
                'reason' => 'Ollama not available - Using RAG with intelligent filtering'
            ];

        } catch (\Exception $e) {
            // Si RAG échoue, réponse minimale
            $this->logger->error('Fallback response error', ['error' => $e->getMessage()]);
            
            return [
                'success' => true,
                'response' => "👋 Bonjour!\n\n" .
                             "Je suis votre assistant AutoLearn. Pour une expérience optimale, " .
                             "veuillez installer Ollama.\n\n" .
                             "En attendant, consultez notre catalogue de cours et événements! 😊",
                'fallback' => true,
                'reason' => 'RAG error - Using minimal response'
            ];
        }
    }

    /**
     * Génère des suggestions de questions
     */
    public function getSuggestions(string $userRole = 'ETUDIANT'): array
    {
        $suggestions = [
            'ETUDIANT' => [
                "Quels cours pour débuter en Python?",
                "Événements cette semaine?",
                "Mon historique d'activités?",
                "Recommande-moi un cours",
                "Comment progresser rapidement?"
            ],
            'ADMIN' => [
                "Combien d'utilisateurs actifs?",
                "Utilisateurs inactifs depuis 7 jours?",
                "Statistiques de la plateforme?",
                "Cours les plus populaires?",
                "Événements à venir?"
            ]
        ];

        return $suggestions[$userRole] ?? $suggestions['ETUDIANT'];
    }

    /**
     * Vérifie le statut du service
     */
    public function getStatus(): array
    {
        $ollamaAvailable = $this->ollamaService->isAvailable();
        $models = $ollamaAvailable ? $this->ollamaService->listModels() : [];

        return [
            'ollama_available' => $ollamaAvailable,
            'models_count' => count($models),
            'models' => array_map(fn($m) => $m['name'] ?? 'unknown', $models),
            'rag_enabled' => true,
            'status' => $ollamaAvailable ? 'operational' : 'degraded'
        ];
    }
}
