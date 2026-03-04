<?php
// Déclaration du fichier PHP

// Définition du namespace pour les services
namespace App\Service;

// Import de l'entité Chapitre pour lier les quiz aux chapitres
use App\Entity\GestionDeCours\Chapitre;
// Import de l'entité Quiz pour créer des quiz
use App\Entity\Quiz;
// Import de l'entité Question pour créer des questions
use App\Entity\Question;
// Import de l'entité Option pour créer des options de réponse
use App\Entity\Option;
// Import de l'EntityManager pour persister les données en base
use Doctrine\ORM\EntityManagerInterface;
// Import du logger pour enregistrer les événements
use Psr\Log\LoggerInterface;
// Import du client HTTP pour appeler l'API Groq
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Service de génération automatique de quiz via l'API Groq
 * Utilise le modèle Grok pour créer des questions pertinentes basées sur le contenu d'un chapitre
 */
class GrokQuizGeneratorService
{
    // URL de l'API Groq (compatible avec l'API OpenAI)
    private const GROQ_API_URL = 'https://api.groq.com/openai/v1/chat/completions';
    // Modèle IA utilisé (Llama 3.3 70B - rapide et performant)
    private const DEFAULT_MODEL = 'llama-3.3-70b-versatile';
    // Nombre maximum de questions autorisées par quiz
    private const MAX_QUESTIONS = 10;

    /**
     * Constructeur avec injection de dépendances
     * 
     * @param HttpClientInterface $httpClient Client HTTP pour les requêtes API
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités Doctrine
     * @param LoggerInterface $logger Logger pour tracer les opérations
     * @param string $grokApiKey Clé API Groq (injectée depuis les variables d'environnement)
     */
    public function __construct(
        private HttpClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private string $grokApiKey
    ) {
    }

    /**
     * Génère un quiz complet pour un chapitre
     */
    // Méthode publique pour générer un quiz complet avec questions et options
    public function genererQuizPourChapitre(
        Chapitre $chapitre,
        int $nombreQuestions = 5,
        array $options = []
    ): Quiz {
        // Enregistre un log d'information au début de la génération
        $this->logger->info('Début de génération de quiz pour le chapitre', [
            // ID du chapitre pour traçabilité
            'chapitre_id' => $chapitre->getId(),
            // Nombre de questions demandées
            'nombre_questions' => $nombreQuestions
        ]);

        // Validation du nombre de questions (entre 1 et MAX_QUESTIONS)
        if ($nombreQuestions < 1 || $nombreQuestions > self::MAX_QUESTIONS) {
            // Lance une exception si le nombre est invalide
            throw new \InvalidArgumentException(
                sprintf('Le nombre de questions doit être entre 1 et %d', self::MAX_QUESTIONS)
            );
        }

        // Crée une nouvelle instance de Quiz
        $quiz = new Quiz();
        // Définit le titre (utilise l'option fournie ou génère un titre par défaut)
        $quiz->setTitre($options['titre'] ?? 'Quiz - ' . $chapitre->getTitre());
        // Définit la description
        $quiz->setDescription($options['description'] ?? 'Quiz généré automatiquement par IA');
        // Définit l'état (par défaut en brouillon pour révision)
        $quiz->setEtat($options['etat'] ?? 'brouillon');
        // Lie le quiz au chapitre
        $quiz->setChapitre($chapitre);
        // Définit le seuil de réussite (par défaut 60%)
        $quiz->setSeuilReussite($options['seuil_reussite'] ?? 60);
        // Définit le nombre maximum de tentatives (null = illimité)
        $quiz->setMaxTentatives($options['max_tentatives'] ?? null);
        // Définit la durée maximale en minutes (null = illimité)
        $quiz->setDureeMaxMinutes($options['duree_max'] ?? null);

        // Appelle l'API Groq pour générer les questions
        $questionsData = $this->appellerApiGroq($chapitre, $nombreQuestions, $options);

        // Parcourt chaque question générée par l'IA
        foreach ($questionsData as $questionData) {
            // Crée une nouvelle instance de Question
            $question = new Question();
            // Définit le texte de la question
            $question->setTexteQuestion($questionData['texte']);
            // Définit le nombre de points (par défaut 10)
            $question->setPoint($questionData['points'] ?? 10);
            // Lie la question au quiz
            $question->setQuiz($quiz);

            // Parcourt chaque option de réponse
            foreach ($questionData['options'] as $optionData) {
                // Crée une nouvelle instance d'Option
                $option = new Option();
                // Définit le texte de l'option
                $option->setTexteOption($optionData['texte']);
                // Définit si l'option est correcte ou non
                $option->setEstCorrecte($optionData['correcte']);
                // Lie l'option à la question
                $option->setQuestion($question);
                
                // Ajoute l'option à la collection de la question
                $question->addOption($option);
            }

            // Ajoute la question à la collection du quiz
            $quiz->addQuestion($question);
        }

        // Prépare le quiz pour être sauvegardé en base de données
        $this->entityManager->persist($quiz);
        // Exécute l'insertion en base de données
        $this->entityManager->flush();

        // Enregistre un log de succès
        $this->logger->info('Quiz généré avec succès', [
            // ID du quiz créé
            'quiz_id' => $quiz->getId(),
            // Nombre de questions effectivement créées
            'nombre_questions' => $quiz->getQuestions()->count()
        ]);

        // Retourne le quiz créé
        return $quiz;
    }


    /**
     * Régénère les questions d'un quiz existant
     */
    // Méthode publique pour régénérer toutes les questions d'un quiz existant
    public function regenererQuestions(Quiz $quiz, int $nombreQuestions = 5, array $options = []): Quiz
    {
        // Enregistre un log d'information au début de la régénération
        $this->logger->info('Régénération des questions du quiz', [
            // ID du quiz à régénérer
            'quiz_id' => $quiz->getId(),
            // Nombre de nouvelles questions à créer
            'nombre_questions' => $nombreQuestions
        ]);

        // Parcourt toutes les questions existantes du quiz
        foreach ($quiz->getQuestions() as $question) {
            // Retire la question de la collection du quiz
            $quiz->removeQuestion($question);
            // Marque la question pour suppression en base de données
            $this->entityManager->remove($question);
        }

        // Exécute la suppression des anciennes questions en base de données
        $this->entityManager->flush();

        // Récupère le chapitre lié au quiz
        $chapitre = $quiz->getChapitre();
        // Appelle l'API Groq pour générer de nouvelles questions
        $questionsData = $this->appellerApiGroq($chapitre, $nombreQuestions, $options);

        // Parcourt chaque nouvelle question générée par l'IA
        foreach ($questionsData as $questionData) {
            // Crée une nouvelle instance de Question
            $question = new Question();
            // Définit le texte de la question
            $question->setTexteQuestion($questionData['texte']);
            // Définit le nombre de points (par défaut 10)
            $question->setPoint($questionData['points'] ?? 10);
            // Lie la question au quiz
            $question->setQuiz($quiz);

            // Parcourt chaque option de réponse
            foreach ($questionData['options'] as $optionData) {
                // Crée une nouvelle instance d'Option
                $option = new Option();
                // Définit le texte de l'option
                $option->setTexteOption($optionData['texte']);
                // Définit si l'option est correcte
                $option->setEstCorrecte($optionData['correcte']);
                // Lie l'option à la question
                $option->setQuestion($question);
                
                // Ajoute l'option à la collection de la question
                $question->addOption($option);
            }

            // Ajoute la question à la collection du quiz
            $quiz->addQuestion($question);
        }

        // Sauvegarde les nouvelles questions en base de données
        $this->entityManager->flush();

        // Enregistre un log de succès
        $this->logger->info('Questions régénérées avec succès', [
            // ID du quiz
            'quiz_id' => $quiz->getId(),
            // Nombre de nouvelles questions créées
            'nombre_questions' => $quiz->getQuestions()->count()
        ]);

        // Retourne le quiz avec les nouvelles questions
        return $quiz;
    }

    /**
     * Appelle l'API Groq pour générer les questions
     */
    // Méthode privée pour communiquer avec l'API Groq et obtenir les questions
    private function appellerApiGroq(Chapitre $chapitre, int $nombreQuestions, array $options): array
    {
        // Récupère le niveau de difficulté depuis les options (par défaut: moyen)
        $difficulte = $options['difficulte'] ?? 'moyen';
        // Extrait le contenu textuel du chapitre
        $contenu = $this->extraireContenuChapitre($chapitre);

        // Construit le prompt (instruction) pour l'IA
        $prompt = $this->construirePrompt($contenu, $nombreQuestions, $difficulte);

        // Bloc try-catch pour gérer les différents types d'erreurs
        try {
            // Envoie une requête POST à l'API Groq
            $response = $this->httpClient->request('POST', self::GROQ_API_URL, [
                // En-têtes HTTP de la requête
                'headers' => [
                    // Token d'authentification Bearer
                    'Authorization' => 'Bearer ' . $this->grokApiKey,
                    // Type de contenu JSON
                    'Content-Type' => 'application/json',
                ],
                // Corps de la requête au format JSON
                'json' => [
                    // Modèle IA à utiliser
                    'model' => self::DEFAULT_MODEL,
                    // Tableau de messages (conversation avec l'IA)
                    'messages' => [
                        [
                            // Message système pour définir le rôle de l'IA
                            'role' => 'system',
                            // Instructions pour l'IA
                            'content' => 'Tu es un expert pédagogique qui crée des quiz de qualité pour évaluer la compréhension des étudiants. Tu réponds uniquement en JSON valide.'
                        ],
                        [
                            // Message utilisateur contenant la demande
                            'role' => 'user',
                            // Le prompt construit précédemment
                            'content' => $prompt
                        ]
                    ],
                    // Température (créativité de l'IA, 0.7 = équilibré)
                    'temperature' => 0.7,
                    // Nombre maximum de tokens dans la réponse
                    'max_tokens' => 2000,
                    // Force la réponse au format JSON
                    'response_format' => ['type' => 'json_object']
                ],
                // Timeout de 60 secondes pour la requête
                'timeout' => 60,
                // Nombre maximum de tentatives en cas d'échec
                'max_retries' => 3,
                // Désactive la vérification SSL du certificat (pour développement)
                'verify_peer' => false,
                // Désactive la vérification du nom d'hôte SSL
                'verify_host' => false
            ]);

            // Convertit la réponse HTTP en tableau PHP
            $data = $response->toArray();
            
            // Vérifie que la réponse contient le contenu attendu
            if (!isset($data['choices'][0]['message']['content'])) {
                // Lance une exception si la structure est invalide
                throw new \RuntimeException('Réponse API invalide: contenu manquant');
            }

            // Extrait le contenu JSON de la réponse
            $content = $data['choices'][0]['message']['content'];
            // Décode le JSON en tableau PHP
            $questionsData = json_decode($content, true);

            // Vérifie qu'il n'y a pas d'erreur de parsing JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Lance une exception avec le message d'erreur JSON
                throw new \RuntimeException('Erreur de parsing JSON: ' . json_last_error_msg());
            }

            // Valide et normalise les données des questions
            return $this->validerEtNormaliserQuestions($questionsData);

        } catch (\Exception $e) {
            // Capture les erreurs de connexion réseau et autres
            $this->logger->error('Erreur de connexion à l\'API Groq', [
                // Message d'erreur
                'error' => $e->getMessage(),
                // ID du chapitre pour traçabilité
                'chapitre_id' => $chapitre->getId()
            ]);
            
            // Lance une exception avec un message explicite pour l'utilisateur
            throw new \RuntimeException(
                'Impossible de se connecter à l\'API Groq. Vérifiez votre connexion internet ou réessayez plus tard. ' .
                'Détails: ' . $e->getMessage(),
                0,
                $e
            );
        } catch (\Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface $e) {
            // Capture les erreurs HTTP (4xx, 5xx)
            $this->logger->error('Erreur HTTP de l\'API Groq', [
                // Code de statut HTTP
                'status_code' => $e->getResponse()->getStatusCode(),
                // Message d'erreur
                'error' => $e->getMessage(),
                // ID du chapitre
                'chapitre_id' => $chapitre->getId()
            ]);
            
            // Lance une exception avec le code HTTP
            throw new \RuntimeException(
                'L\'API Groq a retourné une erreur (Code: ' . $e->getResponse()->getStatusCode() . '). ' .
                'Vérifiez votre clé API ou réessayez plus tard.',
                0,
                $e
            );
        } catch (\Exception $e) {
            // Capture toutes les autres exceptions
            $this->logger->error('Erreur lors de l\'appel à l\'API Groq', [
                // Message d'erreur
                'error' => $e->getMessage(),
                // ID du chapitre
                'chapitre_id' => $chapitre->getId()
            ]);
            // Lance une exception générique
            throw new \RuntimeException('Impossible de générer le quiz: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Extrait le contenu textuel d'un chapitre
     */
    // Méthode privée pour extraire et nettoyer le contenu d'un chapitre
    private function extraireContenuChapitre(Chapitre $chapitre): string
    {
        // Récupère le contenu brut du chapitre
        $contenu = $chapitre->getContenu();
        
        // Supprime toutes les balises HTML pour obtenir du texte pur
        $contenu = strip_tags($contenu);
        
        // Vérifie si le contenu dépasse 4000 caractères
        if (strlen($contenu) > 4000) {
            // Tronque le contenu à 4000 caractères pour respecter les limites de l'API
            $contenu = substr($contenu, 0, 4000) . '...';
        }

        // Retourne le contenu nettoyé et tronqué
        return $contenu;
    }

    /**
     * Construit le prompt pour l'API Groq
     */
    // Méthode privée pour construire l'instruction (prompt) envoyée à l'IA
    private function construirePrompt(string $contenu, int $nombreQuestions, string $difficulte): string
    {
        // Utilise match (PHP 8) pour déterminer le texte de difficulté
        $niveauDifficulte = match($difficulte) {
            // Si difficulté = 'facile'
            'facile' => 'faciles, adaptées aux débutants',
            // Si difficulté = 'difficile'
            'difficile' => 'difficiles, nécessitant une compréhension approfondie',
            // Par défaut (moyen)
            default => 'de difficulté moyenne'
        };

        // Retourne le prompt formaté avec heredoc (<<<PROMPT)
        return <<<PROMPT
Crée exactement {$nombreQuestions} questions à choix multiples (QCM) basées sur le contenu suivant.
Les questions doivent être {$niveauDifficulte}.

CONTENU DU CHAPITRE:
{$contenu}

INSTRUCTIONS:
1. Crée exactement {$nombreQuestions} questions pertinentes
2. Chaque question doit avoir exactement 4 options de réponse
3. Une seule option doit être correcte par question
4. Les questions doivent évaluer la compréhension du contenu
5. Utilise un langage clair et professionnel en français
6. Attribue 10 points par question

RÉPONDS UNIQUEMENT avec un objet JSON dans ce format exact:
{
  "questions": [
    {
      "texte": "Quelle est la question?",
      "points": 10,
      "options": [
        {"texte": "Option A", "correcte": false},
        {"texte": "Option B", "correcte": true},
        {"texte": "Option C", "correcte": false},
        {"texte": "Option D", "correcte": false}
      ]
    }
  ]
}
PROMPT;
    }

    /**
     * Valide et normalise les données de questions reçues de l'API
     */
    // Méthode privée pour valider et normaliser les données JSON reçues de l'IA
    private function validerEtNormaliserQuestions(array $data): array
    {
        // Vérifie que la clé 'questions' existe et est un tableau
        if (!isset($data['questions']) || !is_array($data['questions'])) {
            // Lance une exception si la structure est invalide
            throw new \RuntimeException('Format de réponse invalide: clé "questions" manquante');
        }

        // Initialise un tableau vide pour stocker les questions validées
        $questions = [];

        // Parcourt chaque question avec son index
        foreach ($data['questions'] as $index => $questionData) {
            // Vérifie que le texte de la question existe et n'est pas vide
            if (!isset($questionData['texte']) || empty(trim($questionData['texte']))) {
                // Lance une exception avec le numéro de la question
                throw new \RuntimeException("Question #{$index}: texte manquant");
            }

            // Vérifie que les options existent et sont un tableau
            if (!isset($questionData['options']) || !is_array($questionData['options'])) {
                // Lance une exception
                throw new \RuntimeException("Question #{$index}: options manquantes");
            }

            // Vérifie qu'il y a au moins 2 options
            if (count($questionData['options']) < 2) {
                // Lance une exception
                throw new \RuntimeException("Question #{$index}: au moins 2 options requises");
            }

            // Initialise le flag pour vérifier qu'il y a au moins une réponse correcte
            $hasCorrectAnswer = false;
            // Initialise un tableau vide pour stocker les options validées
            $options = [];

            // Parcourt chaque option avec son index
            foreach ($questionData['options'] as $optionIndex => $optionData) {
                // Vérifie que le texte de l'option existe et n'est pas vide
                if (!isset($optionData['texte']) || empty(trim($optionData['texte']))) {
                    // Lance une exception avec les numéros de question et d'option
                    throw new \RuntimeException("Question #{$index}, Option #{$optionIndex}: texte manquant");
                }

                // Récupère le statut correct/incorrect (par défaut false)
                $isCorrect = $optionData['correcte'] ?? false;
                // Si cette option est correcte
                if ($isCorrect) {
                    // Active le flag
                    $hasCorrectAnswer = true;
                }

                // Ajoute l'option validée au tableau
                $options[] = [
                    // Texte de l'option nettoyé (supprime les espaces inutiles)
                    'texte' => trim($optionData['texte']),
                    // Convertit en booléen pour garantir le type
                    'correcte' => (bool)$isCorrect
                ];
            }

            // Vérifie qu'au moins une option est correcte
            if (!$hasCorrectAnswer) {
                // Lance une exception si aucune réponse correcte n'est définie
                throw new \RuntimeException("Question #{$index}: aucune réponse correcte définie");
            }

            // Ajoute la question validée au tableau final
            $questions[] = [
                // Texte de la question nettoyé
                'texte' => trim($questionData['texte']),
                // Nombre de points (par défaut 10)
                'points' => $questionData['points'] ?? 10,
                // Tableau des options validées
                'options' => $options
            ];
        }

        // Vérifie qu'au moins une question a été générée
        if (empty($questions)) {
            // Lance une exception si aucune question valide
            throw new \RuntimeException('Aucune question valide générée');
        }

        // Retourne le tableau de questions validées et normalisées
        return $questions;
    }
}
