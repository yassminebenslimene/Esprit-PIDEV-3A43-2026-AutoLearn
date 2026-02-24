<?php

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\Question;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Service d'assistant pédagogique conversationnel post-quiz
 * Permet aux étudiants de poser des questions et recevoir des réponses personnalisées
 */
class QuizTutorAIService
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
     * Répond à une question de l'étudiant comme un professeur bienveillant
     * 
     * @param string $questionEtudiant La question posée par l'étudiant
     * @param Quiz $quiz Le quiz concerné
     * @param array $resultDetails Les détails des résultats du quiz
     * @param array $conversationHistory L'historique de la conversation (optionnel)
     * @return array La réponse de l'IA avec métadonnées
     */
    public function repondreQuestion(
        string $questionEtudiant,
        Quiz $quiz,
        array $resultDetails,
        array $conversationHistory = []
    ): array {
        try {
            // Construire le contexte du quiz
            $contexteQuiz = $this->construireContexteQuiz($quiz, $resultDetails);
            
            // Construire les messages pour l'API
            $messages = $this->construireMessages($questionEtudiant, $contexteQuiz, $conversationHistory);
            
            // Appeler l'API Groq
            $response = $this->httpClient->request('POST', self::GROQ_API_URL, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->grokApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => self::DEFAULT_MODEL,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 800,
                ],
                'timeout' => 20
            ]);

            $data = $response->toArray();
            $reponseIA = $data['choices'][0]['message']['content'] ?? '';

            return [
                'success' => true,
                'reponse' => $reponseIA,
                'timestamp' => time(),
                'tokens_used' => $data['usage']['total_tokens'] ?? 0
            ];

        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la réponse du tuteur IA', [
                'error' => $e->getMessage(),
                'question' => $questionEtudiant
            ]);

            return [
                'success' => false,
                'reponse' => $this->genererReponseParDefaut($questionEtudiant),
                'error' => 'Service temporairement indisponible',
                'timestamp' => time()
            ];
        }
    }

    /**
     * Construit le contexte du quiz pour l'IA
     */
    private function construireContexteQuiz(Quiz $quiz, array $resultDetails): string
    {
        $contexte = "CONTEXTE DU QUIZ:\n";
        $contexte .= "Titre: " . $quiz->getTitre() . "\n";
        
        if ($quiz->getChapitre()) {
            $contexte .= "Chapitre: " . $quiz->getChapitre()->getTitre() . "\n";
        }
        
        $contexte .= "\nQUESTIONS ET RÉPONSES DE L'ÉTUDIANT:\n";
        
        $questionNum = 1;
        foreach ($resultDetails as $detail) {
            $question = $detail['question'];
            $isCorrect = $detail['isCorrect'];
            $selectedOptionId = $detail['selectedOption'];
            
            $contexte .= "\nQuestion {$questionNum}: " . $question->getTexteQuestion() . "\n";
            
            // Trouver la réponse sélectionnée et la bonne réponse
            $selectedOption = null;
            $correctOption = null;
            
            foreach ($question->getOptions() as $option) {
                if ($option->getId() === $selectedOptionId) {
                    $selectedOption = $option;
                }
                if ($option->isEstCorrecte()) {
                    $correctOption = $option;
                }
            }
            
            $contexte .= "Réponse de l'étudiant: " . ($selectedOption ? $selectedOption->getTexteOption() : 'Aucune réponse') . "\n";
            $contexte .= "Bonne réponse: " . $correctOption->getTexteOption() . "\n";
            $contexte .= "Résultat: " . ($isCorrect ? "✓ CORRECT" : "✗ INCORRECT") . "\n";
            
            $questionNum++;
        }
        
        return $contexte;
    }

    /**
     * Construit les messages pour l'API avec l'historique de conversation
     */
    private function construireMessages(
        string $questionEtudiant,
        string $contexteQuiz,
        array $conversationHistory
    ): array {
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

        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ]
        ];

        // Ajouter l'historique de conversation
        foreach ($conversationHistory as $exchange) {
            $messages[] = [
                'role' => 'user',
                'content' => $exchange['question']
            ];
            $messages[] = [
                'role' => 'assistant',
                'content' => $exchange['reponse']
            ];
        }

        // Ajouter la question actuelle
        $messages[] = [
            'role' => 'user',
            'content' => $questionEtudiant
        ];

        return $messages;
    }

    /**
     * Génère une réponse par défaut en cas d'erreur
     */
    private function genererReponseParDefaut(string $question): string
    {
        $reponses = [
            "Je suis désolé, je rencontre actuellement des difficultés techniques. Pourriez-vous reformuler votre question ou consulter votre professeur ?",
            "Le service est temporairement indisponible. N'hésitez pas à revoir le chapitre correspondant ou à poser votre question à votre enseignant.",
            "Je ne peux pas répondre pour le moment. Je vous encourage à relire le cours et à noter vos questions pour votre prochain cours."
        ];
        
        return $reponses[array_rand($reponses)];
    }

    /**
     * Génère des suggestions de questions que l'étudiant pourrait poser
     */
    public function genererSuggestionsQuestions(Quiz $quiz, array $resultDetails): array
    {
        $suggestions = [];
        
        // Suggestions basées sur les erreurs
        $questionsIncorrectes = array_filter($resultDetails, fn($detail) => !$detail['isCorrect']);
        
        if (count($questionsIncorrectes) > 0) {
            $suggestions[] = "Pourquoi ma réponse à la question " . (array_key_first($questionsIncorrectes) + 1) . " est incorrecte ?";
            $suggestions[] = "Peux-tu m'expliquer le concept de la question " . (array_key_first($questionsIncorrectes) + 1) . " ?";
        }
        
        // Suggestions générales
        $suggestions[] = "Quels sont les points clés à retenir de ce quiz ?";
        $suggestions[] = "Comment puis-je améliorer ma compréhension de ce sujet ?";
        $suggestions[] = "Peux-tu me donner un exemple pratique ?";
        
        // Limiter à 5 suggestions
        return array_slice($suggestions, 0, 5);
    }
}
