<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class ChallengeCorrectorAIService
{
    private GroqService $groqService;
    private LoggerInterface $logger;

    public function __construct(GroqService $groqService, LoggerInterface $logger)
    {
        $this->groqService = $groqService;
        $this->logger = $logger;
    }

    /**
     * Corrects a user's answer using AI
     * 
     * @param string $question The question text
     * @param string $userAnswer The user's submitted answer
     * @param string $correctAnswer The correct answer from database
     * @param int $maxPoints Maximum points for this question
     * @return array Contains: isCorrect, score, feedback, explanation, advice
     */
    public function correctAnswer(string $question, string $userAnswer, string $correctAnswer, int $maxPoints = 100): array
    {
        try {
            // Check if Groq is available
            if (!$this->groqService->isAvailable()) {
                $this->logger->warning('Groq API not available, using basic comparison');
                return $this->basicCorrection($userAnswer, $correctAnswer, $maxPoints);
            }

            $prompt = $this->buildCorrectionPrompt($question, $userAnswer, $correctAnswer);
            
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'Tu es un assistant pédagogique expert qui évalue les réponses des étudiants avec bienveillance et précision. Tu analyses la compréhension conceptuelle plutôt que la formulation exacte. Tu fournis des explications détaillées et constructives en français.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ];

            $response = $this->groqService->chat($messages, [
                'temperature' => 0.4,
                'max_tokens' => 1000
            ]);

            if (!$response) {
                $this->logger->error('No response from Groq API');
                return $this->basicCorrection($userAnswer, $correctAnswer, $maxPoints);
            }

            // Parse AI response
            $result = $this->parseAIResponse($response, $maxPoints);
            
            if (!$result) {
                $this->logger->error('Failed to parse AI response', ['response' => $response]);
                return $this->basicCorrection($userAnswer, $correctAnswer, $maxPoints);
            }

            return $result;

        } catch (\Exception $e) {
            $this->logger->error('Error in AI correction', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->basicCorrection($userAnswer, $correctAnswer, $maxPoints);
        }
    }

    /**
     * Build the correction prompt for AI
     */
    private function buildCorrectionPrompt(string $question, string $userAnswer, string $correctAnswer): string
    {
        return <<<PROMPT
Tu dois évaluer la réponse d'un étudiant de manière intelligente et pédagogique.

**Question posée:** 
$question

**Réponse correcte attendue:** 
$correctAnswer

**Réponse de l'étudiant:** 
$userAnswer

**Ta mission:**
1. Analyse si la réponse de l'étudiant démontre une compréhension du concept, même si la formulation est différente
2. Identifie les points corrects et les erreurs spécifiques
3. Explique POURQUOI la réponse est correcte ou incorrecte
4. Si incorrecte, explique clairement l'erreur commise et le concept mal compris
5. Donne des conseils concrets et actionnables pour s'améliorer

**Critères d'évaluation:**
- Compréhension conceptuelle (plus important que la formulation exacte)
- Présence des éléments clés de la réponse
- Exactitude factuelle
- Cohérence logique

**Format de réponse OBLIGATOIRE (JSON uniquement):**
{
    "isCorrect": true ou false,
    "score": 0-100 (pourcentage de justesse),
    "feedback": "Message court et encourageant (1-2 phrases)",
    "explanation": "Explication détaillée: Qu'est-ce qui est correct/incorrect? Pourquoi? Quelle est l'erreur de compréhension? (3-5 phrases)",
    "advice": "Conseil concret et actionnable pour s'améliorer ou approfondir (2-3 phrases)"
}

**Important:**
- Sois bienveillant mais honnête
- Si la réponse est partiellement correcte, donne un score proportionnel (ex: 50-70)
- Explique toujours le "pourquoi" de l'erreur, pas seulement le "quoi"
- Fournis des conseils spécifiques, pas génériques
- Réponds UNIQUEMENT en JSON valide, sans texte avant ou après
PROMPT;
    }

    /**
     * Parse AI response into structured data
     */
    private function parseAIResponse(string $response, int $maxPoints): ?array
    {
        // Try to extract JSON from response
        $response = trim($response);
        
        // Remove markdown code blocks if present
        $response = preg_replace('/```json\s*/', '', $response);
        $response = preg_replace('/```\s*$/', '', $response);
        $response = trim($response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('JSON decode error', [
                'error' => json_last_error_msg(),
                'response' => $response
            ]);
            return null;
        }

        // Validate required fields
        if (!isset($data['isCorrect']) || !isset($data['score'])) {
            $this->logger->error('Missing required fields in AI response', ['data' => $data]);
            return null;
        }

        // Calculate actual score based on percentage and max points
        $percentage = max(0, min(100, (int) $data['score']));
        $actualScore = (int) round(($percentage / 100) * $maxPoints);

        return [
            'isCorrect' => (bool) $data['isCorrect'],
            'score' => $actualScore,
            'percentage' => $percentage,
            'feedback' => $data['feedback'] ?? 'Réponse évaluée.',
            'explanation' => $data['explanation'] ?? 'Analyse en cours...',
            'advice' => $data['advice'] ?? 'Continuez à pratiquer.'
        ];
    }

    /**
     * Basic correction without AI (fallback)
     */
    private function basicCorrection(string $userAnswer, string $correctAnswer, int $maxPoints): array
    {
        $correct = strtolower(trim($correctAnswer));
        $user = strtolower(trim($userAnswer));

        // Simple similarity check
        $similarity = 0;
        similar_text($correct, $user, $similarity);

        $isCorrect = $similarity > 80;
        $percentage = (int) $similarity;
        $actualScore = (int) round(($percentage / 100) * $maxPoints);

        return [
            'isCorrect' => $isCorrect,
            'score' => $actualScore,
            'percentage' => $percentage,
            'feedback' => $isCorrect 
                ? 'Bonne réponse!' 
                : 'Réponse incorrecte. Veuillez réviser le contenu.',
            'explanation' => $isCorrect
                ? 'Votre réponse correspond à la réponse attendue.'
                : "La réponse correcte est: $correctAnswer. Votre réponse ne correspond pas aux éléments clés attendus.",
            'advice' => $isCorrect
                ? 'Continuez comme ça! Vous maîtrisez bien ce concept.'
                : 'Relisez attentivement la question et le cours associé. Assurez-vous de bien comprendre les concepts clés avant de répondre.'
        ];
    }

    /**
     * Batch correct multiple answers
     */
    public function correctMultipleAnswers(array $questionsData): array
    {
        $results = [];

        foreach ($questionsData as $index => $data) {
            $results[$index] = $this->correctAnswer(
                $data['question'],
                $data['correctAnswer'],
                $data['userAnswer']
            );
        }

        return $results;
    }
}
