<?php
// Déclaration du fichier PHP

// Définition du namespace pour les services
namespace App\Service;

// Import de l'entité Question
use App\Entity\Question;
// Import de l'entité Option
use App\Entity\Option;
// Import du logger pour tracer les opérations
use Psr\Log\LoggerInterface;
// Import du client HTTP pour appeler l'API Groq
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Service d'agent IA correcteur intelligent
 * Analyse les erreurs des étudiants et fournit des explications pédagogiques personnalisées
 */
class QuizCorrectorAIService
{
    // URL de l'API Groq (compatible OpenAI)
    private const GROQ_API_URL = 'https://api.groq.com/openai/v1/chat/completions';
    // Modèle IA utilisé (Llama 4 Scout - optimisé pour les explications pédagogiques)
    private const DEFAULT_MODEL = 'meta-llama/llama-4-scout-17b-16e-instruct';

    /**
     * Constructeur avec injection de dépendances
     * 
     * @param HttpClientInterface $httpClient Client HTTP pour les requêtes API
     * @param LoggerInterface $logger Logger pour tracer les opérations
     * @param string $grokApiKey Clé API Groq (injectée depuis les variables d'environnement)
     */
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
    // Méthode publique pour générer des explications pour toutes les questions
    public function genererExplicationsPersonnalisees(array $resultDetails): array
    {
        // Initialise un tableau vide pour stocker les explications
        $explications = [];

        // Parcourt chaque question avec son ID et ses détails
        foreach ($resultDetails as $questionId => $detail) {
            // Bloc try-catch pour gérer les erreurs par question
            try {
                // Génère une explication pour cette question spécifique
                $explication = $this->genererExplicationPourQuestion(
                    // Objet Question
                    $detail['question'],
                    // ID de l'option sélectionnée par l'étudiant
                    $detail['selectedOption'],
                    // Booléen indiquant si la réponse est correcte
                    $detail['isCorrect']
                );
                
                // Stocke l'explication dans le tableau avec l'ID de la question comme clé
                $explications[$questionId] = $explication;
            } catch (\Exception $e) {
                // Enregistre l'erreur dans les logs
                $this->logger->error('Erreur lors de la génération d\'explication', [
                    // ID de la question qui a causé l'erreur
                    'question_id' => $questionId,
                    // Message d'erreur
                    'error' => $e->getMessage()
                ]);
                
                // Génère une explication par défaut en cas d'erreur
                $explications[$questionId] = $this->genererExplicationParDefaut($detail);
            }
        }

        // Retourne le tableau complet des explications
        return $explications;
    }

    /**
     * Génère une explication pour une question spécifique
     */
    // Méthode privée pour générer une explication détaillée pour une question
    private function genererExplicationPourQuestion(
        Question $question,
        ?int $selectedOptionId,
        bool $isCorrect
    ): array {
        // Convertit la collection d'options en tableau PHP
        $options = $question->getOptions()->toArray();
        // Initialise la variable pour l'option sélectionnée
        $selectedOption = null;
        // Initialise la variable pour la bonne réponse
        $correctOption = null;

        // Parcourt toutes les options de la question
        foreach ($options as $option) {
            // Trouve l'option sélectionnée par l'étudiant
            if ($option->getId() === $selectedOptionId) {
                $selectedOption = $option;
            }
            // Trouve la bonne réponse
            if ($option->isEstCorrecte()) {
                $correctOption = $option;
            }
        }

        // Construit le prompt pour l'IA en fonction du résultat
        $prompt = $this->construirePromptExplication(
            $question,
            $selectedOption,
            $correctOption,
            $isCorrect
        );

        // Bloc try-catch pour gérer les erreurs d'API
        try {
            // Envoie une requête POST à l'API Groq
            $response = $this->httpClient->request('POST', self::GROQ_API_URL, [
                // En-têtes HTTP
                'headers' => [
                    // Token d'authentification
                    'Authorization' => 'Bearer ' . $this->grokApiKey,
                    // Type de contenu JSON
                    'Content-Type' => 'application/json',
                ],
                // Corps de la requête au format JSON
                'json' => [
                    // Modèle IA à utiliser
                    'model' => self::DEFAULT_MODEL,
                    // Tableau de messages
                    'messages' => [
                        [
                            // Message système définissant le rôle de l'IA
                            'role' => 'system',
                            // Instructions pour l'IA (professeur bienveillant)
                            'content' => 'Tu es un professeur bienveillant et pédagogue qui aide les étudiants à comprendre leurs erreurs. Tu expliques de manière claire, encourageante et constructive. Tu réponds en JSON valide.'
                        ],
                        [
                            // Message utilisateur contenant le prompt
                            'role' => 'user',
                            // Le prompt construit précédemment
                            'content' => $prompt
                        ]
                    ],
                    // Température (créativité, 0.7 = équilibré)
                    'temperature' => 0.7,
                    // Nombre maximum de tokens dans la réponse
                    'max_tokens' => 500,
                    // Force la réponse au format JSON
                    'response_format' => ['type' => 'json_object']
                ],
                // Timeout de 15 secondes
                'timeout' => 15
            ]);

            // Convertit la réponse HTTP en tableau PHP
            $data = $response->toArray();
            // Extrait le contenu JSON de la réponse
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            // Décode le JSON en tableau PHP
            $explication = json_decode($content, true);

            // Retourne un tableau structuré avec l'explication
            return [
                // Message principal (encouragement ou explication)
                'message' => $explication['message'] ?? 'Explication non disponible',
                // Conseil pratique pour progresser
                'conseil' => $explication['conseil'] ?? '',
                // Explication de pourquoi la réponse est incorrecte
                'pourquoi_incorrect' => $explication['pourquoi_incorrect'] ?? '',
                // Explication de pourquoi la bonne réponse est correcte
                'pourquoi_correct' => $explication['pourquoi_correct'] ?? '',
                // Ressources suggérées pour approfondir
                'ressources' => $explication['ressources'] ?? [],
                // Ton du message (success ou error) pour le style CSS
                'tone' => $isCorrect ? 'success' : 'error'
            ];

        } catch (\Exception $e) {
            // Enregistre l'erreur dans les logs
            $this->logger->error('Erreur API Groq pour explication', [
                // Message d'erreur
                'error' => $e->getMessage()
            ]);
            // Relance l'exception pour qu'elle soit gérée par la méthode appelante
            throw $e;
        }
    }

    /**
     * Construit le prompt pour générer l'explication
     */
    // Méthode privée pour construire le prompt adapté selon si la réponse est correcte ou non
    private function construirePromptExplication(
        Question $question,
        ?Option $selectedOption,
        Option $correctOption,
        bool $isCorrect
    ): string {
        // Extrait le texte de la question
        $questionTexte = $question->getTexteQuestion();
        // Extrait le texte de la réponse de l'étudiant (ou "Aucune réponse" si null)
        $reponseEtudiant = $selectedOption ? $selectedOption->getTexteOption() : 'Aucune réponse';
        // Extrait le texte de la bonne réponse
        $bonneReponse = $correctOption->getTexteOption();

        // Si la réponse est correcte, génère un prompt d'encouragement
        if ($isCorrect) {
            // Retourne un prompt pour féliciter et approfondir
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
            // Sinon, génère un prompt pour expliquer l'erreur de manière bienveillante
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
    // Méthode privée pour générer une explication de secours si l'API échoue
    private function genererExplicationParDefaut(array $detail): array
    {
        // Extrait l'objet Question
        $question = $detail['question'];
        // Extrait si la réponse est correcte
        $isCorrect = $detail['isCorrect'];

        // Initialise la variable pour la bonne réponse
        $correctOption = null;
        // Parcourt toutes les options pour trouver la bonne réponse
        foreach ($question->getOptions() as $option) {
            // Si l'option est correcte
            if ($option->isEstCorrecte()) {
                // Stocke l'option correcte
                $correctOption = $option;
                // Sort de la boucle
                break;
            }
        }

        // Si la réponse est correcte
        if ($isCorrect) {
            // Retourne un message d'encouragement simple
            return [
                // Message principal avec emoji
                'message' => '✅ Excellente réponse !',
                // Explication de pourquoi c'est correct
                'pourquoi_correct' => 'Votre réponse est correcte. Continuez ainsi !',
                // Conseil pour progresser
                'conseil' => 'Continuez à approfondir vos connaissances sur ce sujet.',
                // Pas de ressources suggérées
                'ressources' => [],
                // Ton positif pour le style CSS
                'tone' => 'success'
            ];
        } else {
            // Sinon, retourne un message d'erreur simple
            return [
                // Message principal avec emoji
                'message' => '❌ Ce n\'est pas la bonne réponse',
                // Explication de l'erreur
                'pourquoi_incorrect' => 'Votre réponse n\'est pas correcte.',
                // Affiche la bonne réponse
                'pourquoi_correct' => 'La bonne réponse est : ' . ($correctOption ? $correctOption->getTexteOption() : 'Non disponible'),
                // Conseil pour progresser
                'conseil' => 'Révisez ce concept et réessayez.',
                // Ressource suggérée
                'ressources' => ['Relisez le chapitre correspondant'],
                // Ton négatif pour le style CSS
                'tone' => 'error'
            ];
        }
    }

    /**
     * Génère un résumé global des performances avec conseils personnalisés
     */
    // Méthode publique pour générer un bilan pédagogique complet
    public function genererResumePedagogique(array $resultDetails, float $percentage): array
    {
        // Compte le nombre total de questions
        $nombreQuestions = count($resultDetails);
        // Compte le nombre de réponses correctes (fonction anonyme avec array_reduce)
        $nombreCorrect = array_reduce($resultDetails, fn($carry, $detail) => $carry + ($detail['isCorrect'] ? 1 : 0), 0);
        // Calcule le nombre de réponses incorrectes
        $nombreIncorrect = $nombreQuestions - $nombreCorrect;

        // Filtre uniquement les questions incorrectes (fonction anonyme)
        $questionsIncorrectes = array_filter($resultDetails, fn($detail) => !$detail['isCorrect']);
        
        // Construit le prompt pour le résumé pédagogique
        $prompt = $this->construirePromptResume($nombreQuestions, $nombreCorrect, $nombreIncorrect, $percentage, $questionsIncorrectes);

        // Bloc try-catch pour gérer les erreurs d'API
        try {
            // Envoie une requête POST à l'API Groq
            $response = $this->httpClient->request('POST', self::GROQ_API_URL, [
                // En-têtes HTTP
                'headers' => [
                    // Token d'authentification
                    'Authorization' => 'Bearer ' . $this->grokApiKey,
                    // Type de contenu JSON
                    'Content-Type' => 'application/json',
                ],
                // Corps de la requête au format JSON
                'json' => [
                    // Modèle IA à utiliser
                    'model' => self::DEFAULT_MODEL,
                    // Tableau de messages
                    'messages' => [
                        [
                            // Message système définissant le rôle de l'IA
                            'role' => 'system',
                            // Instructions pour l'IA (professeur faisant un bilan)
                            'content' => 'Tu es un professeur qui fait un bilan pédagogique personnalisé. Tu es encourageant, constructif et tu donnes des conseils actionnables.'
                        ],
                        [
                            // Message utilisateur contenant le prompt
                            'role' => 'user',
                            // Le prompt construit précédemment
                            'content' => $prompt
                        ]
                    ],
                    // Température (créativité, 0.7 = équilibré)
                    'temperature' => 0.7,
                    // Nombre maximum de tokens dans la réponse
                    'max_tokens' => 400,
                    // Force la réponse au format JSON
                    'response_format' => ['type' => 'json_object']
                ],
                // Timeout de 15 secondes
                'timeout' => 15
            ]);

            // Convertit la réponse HTTP en tableau PHP
            $data = $response->toArray();
            // Extrait le contenu JSON de la réponse
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            // Décode le JSON en tableau PHP
            $resume = json_decode($content, true);

            // Retourne un tableau structuré avec le résumé pédagogique
            return [
                // Message général adapté au score
                'message_general' => $resume['message_general'] ?? 'Bon travail !',
                // Liste des points forts identifiés
                'points_forts' => $resume['points_forts'] ?? [],
                // Liste des points à améliorer
                'points_amelioration' => $resume['points_amelioration'] ?? [],
                // Liste des conseils de révision
                'conseils_revision' => $resume['conseils_revision'] ?? [],
                // Message d'encouragement final
                'encouragement' => $resume['encouragement'] ?? 'Continuez vos efforts !'
            ];

        } catch (\Exception $e) {
            // Enregistre l'erreur dans les logs
            $this->logger->error('Erreur génération résumé pédagogique', [
                // Message d'erreur
                'error' => $e->getMessage()
            ]);

            // Retourne un résumé par défaut en cas d'erreur
            return $this->genererResumeParDefaut($percentage, $nombreCorrect, $nombreQuestions);
        }
    }

    /**
     * Construit le prompt pour le résumé pédagogique
     */
    // Méthode privée pour construire le prompt du résumé global
    private function construirePromptResume(
        int $nombreQuestions,
        int $nombreCorrect,
        int $nombreIncorrect,
        float $percentage,
        array $questionsIncorrectes
    ): string {
        // Initialise une chaîne vide pour la liste des questions manquées
        $questionsTexte = '';
        // Parcourt chaque question incorrecte
        foreach ($questionsIncorrectes as $detail) {
            // Ajoute le texte de la question à la liste
            $questionsTexte .= "- " . $detail['question']->getTexteQuestion() . "\n";
        }

        // Retourne le prompt formaté avec heredoc
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
    // Méthode privée pour générer un résumé de secours selon le score
    private function genererResumeParDefaut(float $percentage, int $nombreCorrect, int $nombreQuestions): array
    {
        // Si le score est excellent (80% ou plus)
        if ($percentage >= 80) {
            // Retourne un résumé très positif
            return [
                'message_general' => 'Excellent travail ! Vous maîtrisez bien ce sujet.',
                'points_forts' => ['Bonne compréhension globale', 'Excellente performance'],
                'points_amelioration' => ['Continuez à approfondir vos connaissances'],
                'conseils_revision' => ['Explorez des sujets avancés', 'Pratiquez régulièrement'],
                'encouragement' => 'Continuez sur cette lancée !'
            ];
        } elseif ($percentage >= 60) {
            // Si le score est bon (60% à 79%)
            return [
                'message_general' => 'Bon travail ! Vous êtes sur la bonne voie.',
                'points_forts' => ['Bonnes bases acquises'],
                'points_amelioration' => ['Quelques concepts à revoir'],
                'conseils_revision' => ['Relisez les chapitres', 'Pratiquez davantage'],
                'encouragement' => 'Vous progressez bien, continuez !'
            ];
        } else {
            // Si le score est faible (moins de 60%)
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
