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

    public function __construct(
        OllamaService $ollamaService,
        RAGService $ragService,
        LoggerInterface $logger
    ) {
        $this->ollamaService = $ollamaService;
        $this->ragService = $ragService;
        $this->logger = $logger;
    }

    /**
     * Traite une question utilisateur et génère une réponse
     */
    public function ask(string $question, array $options = []): array
    {
        $startTime = microtime(true);

        try {
            // Vérifier si Ollama est disponible
            if (!$this->ollamaService->isAvailable()) {
                return $this->getFallbackResponse($question);
            }

            // 1. Collecter le contexte via RAG
            $context = $this->ragService->retrieveContext($question);

            // 2. Générer la réponse avec Ollama
            $response = $this->ollamaService->generate($question, $context, $options);

            if (!$response) {
                return $this->getFallbackResponse($question);
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
     * Post-traite la réponse pour ajouter des liens et actions
     */
    private function postProcessResponse(string $response, array $context): string
    {
        // Ajouter des liens vers les cours mentionnés
        if (!empty($context['data']['available_courses'])) {
            foreach ($context['data']['available_courses'] as $cours) {
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
        if (!empty($context['data']['upcoming_events'])) {
            foreach ($context['data']['upcoming_events'] as $event) {
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

        return $response;
    }

    /**
     * Réponse de secours si Ollama n'est pas disponible
     */
    private function getFallbackResponse(string $question): array
    {
        $question = strtolower($question);

        // Réponses prédéfinies simples basées sur les mots-clés
        if (preg_match('/(cours|apprendre|python|java|web|programming|étudier)/i', $question)) {
            $response = "🎓 **Nos cours disponibles:**\n\n" .
                       "• **Python** - Idéal pour débuter en programmation\n" .
                       "• **Java** - Pour la programmation orientée objet\n" .
                       "• **Développement Web** - HTML, CSS, JavaScript\n\n" .
                       "💡 Consultez notre catalogue de cours pour plus de détails!";
        } elseif (preg_match('/(événement|event|semaine|mois|particip)/i', $question)) {
            $response = "📅 **Événements à venir:**\n\n" .
                       "Consultez notre page événements pour voir les prochains workshops et meetups!\n\n" .
                       "🔔 Vous pouvez vous inscrire directement depuis la page événements.";
        } elseif (preg_match('/(progrès|statistique|activité|historique|mes cours)/i', $question)) {
            $response = "📊 **Votre progression:**\n\n" .
                       "Consultez votre profil pour voir:\n" .
                       "• Vos cours en cours\n" .
                       "• Vos quiz complétés\n" .
                       "• Votre historique d'activités\n\n" .
                       "💪 Continuez comme ça!";
        } elseif (preg_match('/(aide|help|comment|progresser)/i', $question)) {
            $response = "💡 **Je peux vous aider à:**\n\n" .
                       "• Trouver des cours adaptés à votre niveau\n" .
                       "• Découvrir les événements à venir\n" .
                       "• Suivre vos progrès\n" .
                       "• Naviguer sur la plateforme\n\n" .
                       "Posez-moi une question spécifique! 😊";
        } else {
            $response = "👋 **Bienvenue sur AutoLearn!**\n\n" .
                       "Je suis votre assistant, mais je fonctionne mieux avec Ollama activé.\n\n" .
                       "**En attendant, je peux vous aider avec:**\n" .
                       "• Informations sur les cours\n" .
                       "• Événements à venir\n" .
                       "• Navigation sur la plateforme\n\n" .
                       "💡 **Astuce:** Pour activer l'IA complète, installez Ollama (voir documentation).";
        }

        return [
            'success' => true,
            'response' => $response,
            'fallback' => true,
            'reason' => 'Ollama not available - Using predefined responses'
        ];
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
