<?php
// Déclaration du fichier PHP

// Définition du namespace pour les services
namespace App\Service;

// Import de l'entité Quiz
use App\Entity\Quiz;
// Import de l'entité Question
use App\Entity\Question;
// Import du logger pour tracer les opérations
use Psr\Log\LoggerInterface;
// Import du client HTTP pour appeler l'API Groq
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Service d'assistant pédagogique conversationnel post-quiz
 * Permet aux étudiants de poser des questions et recevoir des réponses personnalisées
 */
class QuizTutorAIService
{
    // URL de l'API Groq (compatible OpenAI)
    private const GROQ_API_URL = 'https://api.groq.com/openai/v1/chat/completions';
    // Modèle IA utilisé (Llama 4 Scout - optimisé pour la conversation)
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
     * Répond à une question de l'étudiant comme un professeur bienveillant
     * 
     * @param string $questionEtudiant La question posée par l'étudiant
     * @param Quiz $quiz Le quiz concerné
     * @param array $resultDetails Les détails des résultats du quiz
     * @param array $conversationHistory L'historique de la conversation (optionnel)
     * @return array La réponse de l'IA avec métadonnées
     */
    // Méthode publique pour répondre à une question de l'étudiant
    public function repondreQuestion(
        string $questionEtudiant,
        Quiz $quiz,
        array $resultDetails,
        array $conversationHistory = []
    ): array {
        // Bloc try-catch pour gérer les erreurs
        try {
            // Construit le contexte du quiz (questions, réponses, résultats)
            $contexteQuiz = $this->construireContexteQuiz($quiz, $resultDetails);
            
            // Construit les messages pour l'API (système + historique + question actuelle)
            $messages = $this->construireMessages($questionEtudiant, $contexteQuiz, $conversationHistory);
            
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
                    // Tableau de messages (conversation)
                    'messages' => $messages,
                    // Température (créativité, 0.7 = équilibré)
                    'temperature' => 0.7,
                    // Nombre maximum de tokens dans la réponse
                    'max_tokens' => 800,
                ],
                // Timeout de 20 secondes
                'timeout' => 20
            ]);

            // Convertit la réponse HTTP en tableau PHP
            $data = $response->toArray();
            // Extrait le contenu de la réponse de l'IA
            $reponseIA = $data['choices'][0]['message']['content'] ?? '';

            // Retourne un tableau avec la réponse et les métadonnées
            return [
                // Indique que la requête a réussi
                'success' => true,
                // Réponse textuelle de l'IA
                'reponse' => $reponseIA,
                // Timestamp de la réponse
                'timestamp' => time(),
                // Nombre de tokens utilisés (pour statistiques)
                'tokens_used' => $data['usage']['total_tokens'] ?? 0
            ];

        } catch (\Exception $e) {
            // Enregistre l'erreur dans les logs
            $this->logger->error('Erreur lors de la réponse du tuteur IA', [
                // Message d'erreur
                'error' => $e->getMessage(),
                // Question qui a causé l'erreur
                'question' => $questionEtudiant
            ]);

            // Retourne une réponse par défaut en cas d'erreur
            return [
                // Indique que la requête a échoué
                'success' => false,
                // Réponse générique de secours
                'reponse' => $this->genererReponseParDefaut($questionEtudiant),
                // Message d'erreur pour l'utilisateur
                'error' => 'Service temporairement indisponible',
                // Timestamp
                'timestamp' => time()
            ];
        }
    }

    /**
     * Construit le contexte du quiz pour l'IA
     */
    // Méthode privée pour construire le contexte textuel du quiz
    private function construireContexteQuiz(Quiz $quiz, array $resultDetails): string
    {
        // Initialise la chaîne de contexte avec un titre
        $contexte = "CONTEXTE DU QUIZ:\n";
        // Ajoute le titre du quiz
        $contexte .= "Titre: " . $quiz->getTitre() . "\n";
        
        // Vérifie si le quiz est lié à un chapitre
        if ($quiz->getChapitre()) {
            // Ajoute le titre du chapitre
            $contexte .= "Chapitre: " . $quiz->getChapitre()->getTitre() . "\n";
        }
        
        // Ajoute un titre de section pour les questions
        $contexte .= "\nQUESTIONS ET RÉPONSES DE L'ÉTUDIANT:\n";
        
        // Initialise le compteur de questions
        $questionNum = 1;
        // Parcourt chaque détail de résultat
        foreach ($resultDetails as $detail) {
            // Extrait l'objet Question
            $question = $detail['question'];
            // Extrait si la réponse est correcte
            $isCorrect = $detail['isCorrect'];
            // Extrait l'ID de l'option sélectionnée
            $selectedOptionId = $detail['selectedOption'];
            
            // Ajoute le numéro et le texte de la question
            $contexte .= "\nQuestion {$questionNum}: " . $question->getTexteQuestion() . "\n";
            
            // Initialise les variables pour les options
            $selectedOption = null;
            $correctOption = null;
            
            // Parcourt toutes les options de la question
            foreach ($question->getOptions() as $option) {
                // Trouve l'option sélectionnée par l'étudiant
                if ($option->getId() === $selectedOptionId) {
                    $selectedOption = $option;
                }
                // Trouve la bonne réponse
                if ($option->isEstCorrecte()) {
                    $correctOption = $option;
                }
            }
            
            // Ajoute la réponse de l'étudiant au contexte
            $contexte .= "Réponse de l'étudiant: " . ($selectedOption ? $selectedOption->getTexteOption() : 'Aucune réponse') . "\n";
            // Ajoute la bonne réponse au contexte
            $contexte .= "Bonne réponse: " . $correctOption->getTexteOption() . "\n";
            // Ajoute le résultat (correct ou incorrect) avec des symboles visuels
            $contexte .= "Résultat: " . ($isCorrect ? "✓ CORRECT" : "✗ INCORRECT") . "\n";
            
            // Incrémente le compteur de questions
            $questionNum++;
        }
        
        // Retourne le contexte complet
        return $contexte;
    }

    /**
     * Construit les messages pour l'API avec l'historique de conversation
     */
    // Méthode privée pour construire le tableau de messages pour l'API
    private function construireMessages(
        string $questionEtudiant,
        string $contexteQuiz,
        array $conversationHistory
    ): array {
        // Définit le prompt système qui configure le comportement de l'IA
        $systemPrompt = <<<PROMPT
Tu es un professeur bienveillant et pédagogue qui aide les étudiants après un quiz.

TON RÔLE:
- Répondre aux questions des étudiants sur le quiz qu'ils viennent de passer
- Expliquer les concepts de manière claire et accessible
- Encourager l'apprentissage et la compréhension profonde
- Donner des exemples concrets et des analogies
- Adapter ton niveau d'explication au besoin de l'étudiant

TON STYLE:
- Bienveillant et encourageant
- Pédagogique et clair
- Patient et à l'écoute
- Utilise des exemples pratiques
- Évite le jargon inutile
- Pose des questions pour vérifier la compréhension

CE QUE TU NE DOIS PAS FAIRE:
- Ne donne pas directement toutes les réponses sans explication
- N'utilise pas un ton condescendant
- Ne décourage jamais l'étudiant
- Ne sors pas du contexte du quiz et du chapitre

{$contexteQuiz}
PROMPT;

        // Initialise le tableau de messages avec le message système
        $messages = [
            [
                // Rôle système pour définir le comportement de l'IA
                'role' => 'system',
                // Contenu du prompt système avec le contexte du quiz
                'content' => $systemPrompt
            ]
        ];

        // Parcourt l'historique de conversation
        foreach ($conversationHistory as $exchange) {
            // Ajoute la question précédente de l'étudiant
            $messages[] = [
                // Rôle utilisateur
                'role' => 'user',
                // Question posée précédemment
                'content' => $exchange['question']
            ];
            // Ajoute la réponse précédente de l'IA
            $messages[] = [
                // Rôle assistant (l'IA)
                'role' => 'assistant',
                // Réponse donnée précédemment
                'content' => $exchange['reponse']
            ];
        }

        // Ajoute la question actuelle de l'étudiant
        $messages[] = [
            // Rôle utilisateur
            'role' => 'user',
            // Question actuelle
            'content' => $questionEtudiant
        ];

        // Retourne le tableau complet de messages
        return $messages;
    }

    /**
     * Génère une réponse par défaut en cas d'erreur
     */
    // Méthode privée pour générer une réponse de secours
    private function genererReponseParDefaut(string $question): string
    {
        // Tableau de réponses génériques possibles
        $reponses = [
            "Je suis désolé, je rencontre actuellement des difficultés techniques. Pourriez-vous reformuler votre question ou consulter votre professeur ?",
            "Le service est temporairement indisponible. N'hésitez pas à revoir le chapitre correspondant ou à poser votre question à votre enseignant.",
            "Je ne peux pas répondre pour le moment. Je vous encourage à relire le cours et à noter vos questions pour votre prochain cours."
        ];
        
        // Retourne une réponse aléatoire du tableau
        return $reponses[array_rand($reponses)];
    }

    /**
     * Génère des suggestions de questions que l'étudiant pourrait poser
     */
    // Méthode publique pour générer des suggestions de questions
    public function genererSuggestionsQuestions(Quiz $quiz, array $resultDetails): array
    {
        // Initialise un tableau vide pour les suggestions
        $suggestions = [];
        
        // Filtre les questions incorrectes (fonction anonyme)
        $questionsIncorrectes = array_filter($resultDetails, fn($detail) => !$detail['isCorrect']);
        
        // Vérifie s'il y a des questions incorrectes
        if (count($questionsIncorrectes) > 0) {
            // Ajoute une suggestion sur la première question incorrecte
            $suggestions[] = "Pourquoi ma réponse à la question " . (array_key_first($questionsIncorrectes) + 1) . " est incorrecte ?";
            // Ajoute une suggestion pour expliquer le concept
            $suggestions[] = "Peux-tu m'expliquer le concept de la question " . (array_key_first($questionsIncorrectes) + 1) . " ?";
        }
        
        // Ajoute des suggestions générales
        $suggestions[] = "Quels sont les points clés à retenir de ce quiz ?";
        $suggestions[] = "Comment puis-je améliorer ma compréhension de ce sujet ?";
        $suggestions[] = "Peux-tu me donner un exemple pratique ?";
        
        // Limite le tableau à 5 suggestions maximum
        return array_slice($suggestions, 0, 5);
    }
}
