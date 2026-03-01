<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class ExerciceGeneratorAIService
{
    private GroqService $groqService;
    private LoggerInterface $logger;

    public function __construct(GroqService $groqService, LoggerInterface $logger)
    {
        $this->groqService = $groqService;
        $this->logger = $logger;
    }

    /**
     * Génère automatiquement des exercices en utilisant l'IA Groq
     * 
     * @param string $sujet Le sujet des exercices à générer
     * @param string $niveau Le niveau de difficulté (Débutant, Intermédiaire, Avancé)
     * @param int $nombre Nombre d'exercices à générer (par défaut 5)
     * @return array Liste des exercices générés avec question, réponse et points
     */
    public function generateExercices(string $sujet, string $niveau, int $nombre = 5): array
    {
        try {
            // Vérifier si Groq est disponible
            if (!$this->groqService->isAvailable()) {
                $this->logger->warning('Groq API not available for exercise generation');
                throw new \Exception('Le service de génération IA n\'est pas disponible actuellement.');
            }

            $prompt = $this->buildGenerationPrompt($sujet, $niveau, $nombre);
            
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'Tu es un expert pédagogique qui crée des exercices éducatifs de qualité. Tu génères des exercices adaptés au niveau demandé avec des questions claires et des réponses précises. Tu réponds UNIQUEMENT en JSON valide.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ];

            $response = $this->groqService->chat($messages, [
                'temperature' => 0.7,
                'max_tokens' => 2000
            ]);

            if (!$response) {
                $this->logger->error('No response from Groq API for exercise generation');
                throw new \Exception('Aucune réponse de l\'IA. Veuillez réessayer.');
            }

            // Parser la réponse de l'IA
            $exercices = $this->parseAIResponse($response);
            
            if (empty($exercices)) {
                $this->logger->error('Failed to parse AI response for exercises', ['response' => $response]);
                throw new \Exception('Impossible de générer les exercices. Format de réponse invalide.');
            }

            return $exercices;

        } catch (\Exception $e) {
            $this->logger->error('Error in exercise generation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Construit le prompt pour la génération d'exercices
     */
    private function buildGenerationPrompt(string $sujet, string $niveau, int $nombre): string
    {
        $niveauDescription = match($niveau) {
            'Débutant' => 'niveau débutant (questions simples, concepts de base)',
            'Intermédiaire' => 'niveau intermédiaire (questions moyennement complexes, concepts avancés)',
            'Avancé' => 'niveau avancé (questions complexes, concepts experts)',
            default => 'niveau intermédiaire'
        };

        $pointsRange = match($niveau) {
            'Débutant' => '5-10 points',
            'Intermédiaire' => '10-15 points',
            'Avancé' => '15-20 points',
            default => '10-15 points'
        };

        return <<<PROMPT
Génère exactement {$nombre} exercices sur le sujet suivant: "{$sujet}"

**Niveau de difficulté:** {$niveauDescription}
**Points par exercice:** {$pointsRange}

**Consignes importantes:**
1. Chaque exercice doit avoir une question claire et précise
2. Chaque réponse doit être complète et correcte
3. Les questions doivent être variées et couvrir différents aspects du sujet
4. Adapte la complexité au niveau demandé
5. Les réponses doivent être concises mais complètes (2-4 phrases maximum)

**Format de réponse OBLIGATOIRE (JSON uniquement):**
{
    "exercices": [
        {
            "question": "Question claire et précise ici",
            "reponse": "Réponse complète et correcte ici",
            "points": 10
        }
    ]
}

**Important:**
- Génère EXACTEMENT {$nombre} exercices
- Réponds UNIQUEMENT en JSON valide, sans texte avant ou après
- Assure-toi que les questions sont pertinentes et éducatives
- Les points doivent être dans la fourchette {$pointsRange}
PROMPT;
    }

    /**
     * Parse la réponse de l'IA en tableau d'exercices
     */
    private function parseAIResponse(string $response): array
    {
        // Nettoyer la réponse
        $response = trim($response);
        
        // Supprimer les blocs markdown si présents
        $response = preg_replace('/```json\s*/', '', $response);
        $response = preg_replace('/```\s*$/', '', $response);
        $response = trim($response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('JSON decode error in exercise generation', [
                'error' => json_last_error_msg(),
                'response' => $response
            ]);
            return [];
        }

        // Valider la structure
        if (!isset($data['exercices']) || !is_array($data['exercices'])) {
            $this->logger->error('Invalid structure in AI response', ['data' => $data]);
            return [];
        }

        $exercices = [];
        foreach ($data['exercices'] as $exercice) {
            // Valider chaque exercice
            if (!isset($exercice['question']) || !isset($exercice['reponse']) || !isset($exercice['points'])) {
                $this->logger->warning('Skipping invalid exercise', ['exercice' => $exercice]);
                continue;
            }

            $exercices[] = [
                'question' => trim($exercice['question']),
                'reponse' => trim($exercice['reponse']),
                'points' => max(1, min(100, (int) $exercice['points']))
            ];
        }

        return $exercices;
    }
}
