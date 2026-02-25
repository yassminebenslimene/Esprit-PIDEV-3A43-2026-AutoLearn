<?php

namespace App\Service;

use App\Entity\GestionDeCours\Chapitre;
use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Option;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Service de génération automatique de quiz via l'API Groq
 * Utilise le modèle Grok pour créer des questions pertinentes basées sur le contenu d'un chapitre
 */
class GrokQuizGeneratorService
{
    private const GROQ_API_URL = 'https://api.groq.com/openai/v1/chat/completions';
    private const DEFAULT_MODEL = 'meta-llama/llama-4-scout-17b-16e-instruct';
    private const MAX_QUESTIONS = 10;

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
    public function genererQuizPourChapitre(
        Chapitre $chapitre,
        int $nombreQuestions = 5,
        array $options = []
    ): Quiz {
        $this->logger->info('Début de génération de quiz pour le chapitre', [
            'chapitre_id' => $chapitre->getId(),
            'nombre_questions' => $nombreQuestions
        ]);

        // Validation
        if ($nombreQuestions < 1 || $nombreQuestions > self::MAX_QUESTIONS) {
            throw new \InvalidArgumentException(
                sprintf('Le nombre de questions doit être entre 1 et %d', self::MAX_QUESTIONS)
            );
        }

        // Créer le quiz
        $quiz = new Quiz();
        $quiz->setTitre($options['titre'] ?? 'Quiz - ' . $chapitre->getTitre());
        $quiz->setDescription($options['description'] ?? 'Quiz généré automatiquement par IA');
        $quiz->setEtat($options['etat'] ?? 'brouillon');
        $quiz->setChapitre($chapitre);
        $quiz->setSeuilReussite($options['seuil_reussite'] ?? 60);
        $quiz->setMaxTentatives($options['max_tentatives'] ?? null);
        $quiz->setDureeMaxMinutes($options['duree_max'] ?? null);

        // Générer les questions via l'API Groq
        $questionsData = $this->appellerApiGroq($chapitre, $nombreQuestions, $options);

        // Créer les entités Question et Option
        foreach ($questionsData as $questionData) {
            $question = new Question();
            $question->setTexteQuestion($questionData['texte']);
            $question->setPoint($questionData['points'] ?? 10);
            $question->setQuiz($quiz);

            foreach ($questionData['options'] as $optionData) {
                $option = new Option();
                $option->setTexteOption($optionData['texte']);
                $option->setEstCorrecte($optionData['correcte']);
                $option->setQuestion($question);
                
                $question->addOption($option);
            }

            $quiz->addQuestion($question);
        }

        // Persister en base de données
        $this->entityManager->persist($quiz);
        $this->entityManager->flush();

        $this->logger->info('Quiz généré avec succès', [
            'quiz_id' => $quiz->getId(),
            'nombre_questions' => $quiz->getQuestions()->count()
        ]);

        return $quiz;
    }


    /**
     * Régénère les questions d'un quiz existant
     */
    public function regenererQuestions(Quiz $quiz, int $nombreQuestions = 5, array $options = []): Quiz
    {
        $this->logger->info('Régénération des questions du quiz', [
            'quiz_id' => $quiz->getId(),
            'nombre_questions' => $nombreQuestions
        ]);

        // Supprimer les anciennes questions
        foreach ($quiz->getQuestions() as $question) {
            $quiz->removeQuestion($question);
            $this->entityManager->remove($question);
        }

        $this->entityManager->flush();

        // Générer de nouvelles questions
        $chapitre = $quiz->getChapitre();
        $questionsData = $this->appellerApiGroq($chapitre, $nombreQuestions, $options);

        foreach ($questionsData as $questionData) {
            $question = new Question();
            $question->setTexteQuestion($questionData['texte']);
            $question->setPoint($questionData['points'] ?? 10);
            $question->setQuiz($quiz);

            foreach ($questionData['options'] as $optionData) {
                $option = new Option();
                $option->setTexteOption($optionData['texte']);
                $option->setEstCorrecte($optionData['correcte']);
                $option->setQuestion($question);
                
                $question->addOption($option);
            }

            $quiz->addQuestion($question);
        }

        $this->entityManager->flush();

        $this->logger->info('Questions régénérées avec succès', [
            'quiz_id' => $quiz->getId(),
            'nombre_questions' => $quiz->getQuestions()->count()
        ]);

        return $quiz;
    }

    /**
     * Appelle l'API Groq pour générer les questions
     */
    private function appellerApiGroq(Chapitre $chapitre, int $nombreQuestions, array $options): array
    {
        $difficulte = $options['difficulte'] ?? 'moyen';
        $contenu = $this->extraireContenuChapitre($chapitre);

        $prompt = $this->construirePrompt($contenu, $nombreQuestions, $difficulte);

        try {
            $response = $this->httpClient->request('POST', self::GROQ_API_URL, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->grokApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => self::DEFAULT_MODEL,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un expert pédagogique qui crée des quiz de qualité pour évaluer la compréhension des étudiants. Tu réponds uniquement en JSON valide.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                    'response_format' => ['type' => 'json_object']
                ],
                'timeout' => 30
            ]);

            $data = $response->toArray();
            
            if (!isset($data['choices'][0]['message']['content'])) {
                throw new \RuntimeException('Réponse API invalide: contenu manquant');
            }

            $content = $data['choices'][0]['message']['content'];
            $questionsData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Erreur de parsing JSON: ' . json_last_error_msg());
            }

            return $this->validerEtNormaliserQuestions($questionsData);

        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'appel à l\'API Groq', [
                'error' => $e->getMessage(),
                'chapitre_id' => $chapitre->getId()
            ]);
            throw new \RuntimeException('Impossible de générer le quiz: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Extrait le contenu textuel d'un chapitre
     */
    private function extraireContenuChapitre(Chapitre $chapitre): string
    {
        $contenu = $chapitre->getContenu();
        
        // Nettoyer le HTML si présent
        $contenu = strip_tags($contenu);
        
        // Limiter la longueur pour ne pas dépasser les limites de l'API
        if (strlen($contenu) > 4000) {
            $contenu = substr($contenu, 0, 4000) . '...';
        }

        return $contenu;
    }

    /**
     * Construit le prompt pour l'API Groq
     */
    private function construirePrompt(string $contenu, int $nombreQuestions, string $difficulte): string
    {
        $niveauDifficulte = match($difficulte) {
            'facile' => 'faciles, adaptées aux débutants',
            'difficile' => 'difficiles, nécessitant une compréhension approfondie',
            default => 'de difficulté moyenne'
        };

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
    private function validerEtNormaliserQuestions(array $data): array
    {
        if (!isset($data['questions']) || !is_array($data['questions'])) {
            throw new \RuntimeException('Format de réponse invalide: clé "questions" manquante');
        }

        $questions = [];

        foreach ($data['questions'] as $index => $questionData) {
            // Validation de la question
            if (!isset($questionData['texte']) || empty(trim($questionData['texte']))) {
                throw new \RuntimeException("Question #{$index}: texte manquant");
            }

            if (!isset($questionData['options']) || !is_array($questionData['options'])) {
                throw new \RuntimeException("Question #{$index}: options manquantes");
            }

            if (count($questionData['options']) < 2) {
                throw new \RuntimeException("Question #{$index}: au moins 2 options requises");
            }

            // Validation des options
            $hasCorrectAnswer = false;
            $options = [];

            foreach ($questionData['options'] as $optionIndex => $optionData) {
                if (!isset($optionData['texte']) || empty(trim($optionData['texte']))) {
                    throw new \RuntimeException("Question #{$index}, Option #{$optionIndex}: texte manquant");
                }

                $isCorrect = $optionData['correcte'] ?? false;
                if ($isCorrect) {
                    $hasCorrectAnswer = true;
                }

                $options[] = [
                    'texte' => trim($optionData['texte']),
                    'correcte' => (bool)$isCorrect
                ];
            }

            if (!$hasCorrectAnswer) {
                throw new \RuntimeException("Question #{$index}: aucune réponse correcte définie");
            }

            $questions[] = [
                'texte' => trim($questionData['texte']),
                'points' => $questionData['points'] ?? 10,
                'options' => $options
            ];
        }

        if (empty($questions)) {
            throw new \RuntimeException('Aucune question valide générée');
        }

        return $questions;
    }
}
