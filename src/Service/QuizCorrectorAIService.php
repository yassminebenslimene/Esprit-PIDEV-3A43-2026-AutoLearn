<?php

namespace App\Service;

use App\Entity\Question;
use App\Entity\Option;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Service d'agent IA correcteur intelligent
 * Analyse les erreurs des étudiants et fournit des explications pédagogiques personnalisées
 */
class QuizCorrectorAIService
{
    private const GROQ_API_URL = 'https://api.groq.com/openai/v1/chat/completions';
    private const DEFAULT_MODEL = 'meta-llama/llama-4-scout-17b-16e-instruct';

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $grokApiKey
    ) {
    }

    /**
     * Génère des explications personnalisées pour toutes les réponses d'un étudiant
     * 
     * @param array $resultDetails Détails des réponses (format: ['questionId' => ['question' => Question, 'selectedOption' => int, 'isCorrect' => bool]])
     * @return array Explications pour chaque question
     */
    public function genererExplicationsPersonnalisees(array $resultDetails): array
    {
        $explications = [];

        foreach ($resultDetails as $questionId => $detail) {
            try {
                $explication = $this->genererExplicationPourQuestion(
                    $detail['question'],
                    $detail['selectedOption'],
                    $detail['isCorrect']
                );
                
                $explications[$questionId] = $explication;
            } catch (\Exception $e) {
                $this->logger->error('Erreur lors de la génération d\'explication', [
                    'question_id' => $questionId,
                    'error' => $e->getMessage()
                ]);
                
                // Explication par défaut en cas d'erreur
                $explications[$questionId] = $this->genererExplicationParDefaut($detail);
            }
        }

        return $explications;
    }

    /**
     * Génère une explication pour une question spécifique
     */
    private function genererExplicationPourQuestion(
        Question $question,
        ?int $selectedOptionId,
        bool $isCorrect
    ): array {
        $options = $question->getOptions()->toArray();
        $selectedOption = null;
        $correctOption = null;

        foreach ($options as $option) {
            if ($option->getId() === $selectedOptionId) {
                $selectedOption = $option;
            }
            if ($option->isEstCorrecte()) {
                $correctOption = $option;
            }
        }

        $prompt = $this->construirePromptExplication(
            $question,
            $selectedOption,
            $correctOption,
            $isCorrect
        );

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
                            'content' => 'Tu es un professeur bienveillant et pédagogue qui aide les étudiants à comprendre leurs erreurs. Tu expliques de manière claire, encourageante et constructive. Tu réponds en JSON valide.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                    'response_format' => ['type' => 'json_object']
                ],
                'timeout' => 15
            ]);

            $data = $response->toArray();
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            $explication = json_decode($content, true);

            return [
                'message' => $explication['message'] ?? 'Explication non disponible',
                'conseil' => $explication['conseil'] ?? '',
                'pourquoi_incorrect' => $explication['pourquoi_incorrect'] ?? '',
                'pourquoi_correct' => $explication['pourquoi_correct'] ?? '',
                'ressources' => $explication['ressources'] ?? [],
                'tone' => $isCorrect ? 'success' : 'error'
            ];

        } catch (\Exception $e) {
            $this->logger->error('Erreur API Groq pour explication', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Construit le prompt pour générer l'explication
     */
    private function construirePromptExplication(
        Question $question,
        ?Option $selectedOption,
        Option $correctOption,
        bool $isCorrect
    ): string {
        $questionTexte = $question->getTexteQuestion();
        $reponseEtudiant = $selectedOption ? $selectedOption->getTexteOption() : 'Aucune réponse';
        $bonneReponse = $correctOption->getTexteOption();

        if ($isCorrect) {
            return <<<PROMPT
L'étudiant a CORRECTEMENT répondu à cette question:

QUESTION: {$questionTexte}
RÉPONSE DE L'ÉTUDIANT: {$reponseEtudiant}
BONNE RÉPONSE: {$bonneReponse}

Génère un message d'encouragement et une explication pédagogique qui:
1. Félicite l'étudiant pour sa bonne réponse
2. Explique POURQUOI cette réponse est correcte
3. Approfondit le concept pour renforcer la compréhension
4. Suggère des ressources pour aller plus loin (optionnel)

RÉPONDS en JSON avec ce format:
{
  "message": "Message d'encouragement positif et bref",
  "pourquoi_correct": "Explication détaillée de pourquoi c'est correct",
  "conseil": "Conseil pour approfondir ou conseil d'application pratique",
  "ressources": ["Suggestion 1", "Suggestion 2"]
}
PROMPT;
        } else {
            return <<<PROMPT
L'étudiant a INCORRECTEMENT répondu à cette question:

QUESTION: {$questionTexte}
RÉPONSE DE L'ÉTUDIANT: {$reponseEtudiant}
BONNE RÉPONSE: {$bonneReponse}

Génère une explication pédagogique bienveillante qui:
1. Explique POURQUOI la réponse de l'étudiant est incorrecte (sans être négatif)
2. Explique POURQUOI la bonne réponse est correcte
3. Donne un conseil pratique pour éviter cette erreur à l'avenir
4. Encourage l'étudiant à continuer d'apprendre

Ton: Bienveillant, constructif, pédagogique, encourageant

RÉPONDS en JSON avec ce format:
{
  "message": "Message d'encouragement bref et positif",
  "pourquoi_incorrect": "Explication claire de l'erreur commise",
  "pourquoi_correct": "Explication de la bonne réponse",
  "conseil": "Conseil pratique pour progresser",
  "ressources": ["Suggestion de révision 1", "Suggestion 2"]
}
PROMPT;
        }
    }

    /**
     * Génère une explication par défaut en cas d'erreur API
     */
    private function genererExplicationParDefaut(array $detail): array
    {
        $question = $detail['question'];
        $isCorrect = $detail['isCorrect'];

        $correctOption = null;
        foreach ($question->getOptions() as $option) {
            if ($option->isEstCorrecte()) {
                $correctOption = $option;
                break;
            }
        }

        if ($isCorrect) {
            return [
                'message' => '✅ Excellente réponse !',
                'pourquoi_correct' => 'Votre réponse est correcte. Continuez ainsi !',
                'conseil' => 'Continuez à approfondir vos connaissances sur ce sujet.',
                'ressources' => [],
                'tone' => 'success'
            ];
        } else {
            return [
                'message' => '❌ Ce n\'est pas la bonne réponse',
                'pourquoi_incorrect' => 'Votre réponse n\'est pas correcte.',
                'pourquoi_correct' => 'La bonne réponse est : ' . ($correctOption ? $correctOption->getTexteOption() : 'Non disponible'),
                'conseil' => 'Révisez ce concept et réessayez.',
                'ressources' => ['Relisez le chapitre correspondant'],
                'tone' => 'error'
            ];
        }
    }

    /**
     * Génère un résumé global des performances avec conseils personnalisés
     */
    public function genererResumePedagogique(array $resultDetails, float $percentage): array
    {
        $nombreQuestions = count($resultDetails);
        $nombreCorrect = array_reduce($resultDetails, fn($carry, $detail) => $carry + ($detail['isCorrect'] ? 1 : 0), 0);
        $nombreIncorrect = $nombreQuestions - $nombreCorrect;

        // Identifier les thèmes des erreurs
        $questionsIncorrectes = array_filter($resultDetails, fn($detail) => !$detail['isCorrect']);
        
        $prompt = $this->construirePromptResume($nombreQuestions, $nombreCorrect, $nombreIncorrect, $percentage, $questionsIncorrectes);

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
                            'content' => 'Tu es un professeur qui fait un bilan pédagogique personnalisé. Tu es encourageant, constructif et tu donnes des conseils actionnables.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 400,
                    'response_format' => ['type' => 'json_object']
                ],
                'timeout' => 15
            ]);

            $data = $response->toArray();
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            $resume = json_decode($content, true);

            return [
                'message_general' => $resume['message_general'] ?? 'Bon travail !',
                'points_forts' => $resume['points_forts'] ?? [],
                'points_amelioration' => $resume['points_amelioration'] ?? [],
                'conseils_revision' => $resume['conseils_revision'] ?? [],
                'encouragement' => $resume['encouragement'] ?? 'Continuez vos efforts !'
            ];

        } catch (\Exception $e) {
            $this->logger->error('Erreur génération résumé pédagogique', [
                'error' => $e->getMessage()
            ]);

            return $this->genererResumeParDefaut($percentage, $nombreCorrect, $nombreQuestions);
        }
    }

    /**
     * Construit le prompt pour le résumé pédagogique
     */
    private function construirePromptResume(
        int $nombreQuestions,
        int $nombreCorrect,
        int $nombreIncorrect,
        float $percentage,
        array $questionsIncorrectes
    ): string {
        $questionsTexte = '';
        foreach ($questionsIncorrectes as $detail) {
            $questionsTexte .= "- " . $detail['question']->getTexteQuestion() . "\n";
        }

        return <<<PROMPT
Génère un bilan pédagogique personnalisé pour un étudiant qui vient de terminer un quiz:

STATISTIQUES:
- Total de questions: {$nombreQuestions}
- Réponses correctes: {$nombreCorrect}
- Réponses incorrectes: {$nombreIncorrect}
- Score: {$percentage}%

QUESTIONS MANQUÉES:
{$questionsTexte}

Génère un bilan qui:
1. Donne un message général adapté au score (encourageant même si faible)
2. Identifie les points forts (ce qui a été réussi)
3. Identifie les points à améliorer (sans être négatif)
4. Donne 2-3 conseils concrets de révision
5. Termine par un encouragement motivant

RÉPONDS en JSON avec ce format:
{
  "message_general": "Message d'ouverture adapté au score",
  "points_forts": ["Point fort 1", "Point fort 2"],
  "points_amelioration": ["Domaine à revoir 1", "Domaine 2"],
  "conseils_revision": ["Conseil pratique 1", "Conseil 2", "Conseil 3"],
  "encouragement": "Message final motivant"
}
PROMPT;
    }

    /**
     * Génère un résumé par défaut
     */
    private function genererResumeParDefaut(float $percentage, int $nombreCorrect, int $nombreQuestions): array
    {
        if ($percentage >= 80) {
            return [
                'message_general' => 'Excellent travail ! Vous maîtrisez bien ce sujet.',
                'points_forts' => ['Bonne compréhension globale', 'Excellente performance'],
                'points_amelioration' => ['Continuez à approfondir vos connaissances'],
                'conseils_revision' => ['Explorez des sujets avancés', 'Pratiquez régulièrement'],
                'encouragement' => 'Continuez sur cette lancée !'
            ];
        } elseif ($percentage >= 60) {
            return [
                'message_general' => 'Bon travail ! Vous êtes sur la bonne voie.',
                'points_forts' => ['Bonnes bases acquises'],
                'points_amelioration' => ['Quelques concepts à revoir'],
                'conseils_revision' => ['Relisez les chapitres', 'Pratiquez davantage'],
                'encouragement' => 'Vous progressez bien, continuez !'
            ];
        } else {
            return [
                'message_general' => 'Ne vous découragez pas, l\'apprentissage prend du temps.',
                'points_forts' => ['Vous avez réussi ' . $nombreCorrect . ' questions'],
                'points_amelioration' => ['Plusieurs concepts à revoir'],
                'conseils_revision' => ['Relisez attentivement le cours', 'Prenez des notes', 'Refaites le quiz'],
                'encouragement' => 'Chaque erreur est une opportunité d\'apprendre !'
            ];
        }
    }
}
