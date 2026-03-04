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
        $maxRetries = 2;
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            try {
                $attempt++;
                
                // Vérifier si Groq est disponible
                if (!$this->groqService->isAvailable()) {
                    $this->logger->warning('Groq API not available for exercise generation');
                    throw new \Exception('Le service de génération IA n\'est pas disponible actuellement.');
                }

                $prompt = $this->buildGenerationPrompt($sujet, $niveau, $nombre);
                
                $messages = [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un expert pédagogique. Crée des exercices avec des réponses détaillées (3-4 phrases, 120-250 caractères). Réponds UNIQUEMENT en JSON valide, sans texte avant/après. Commence par { et termine par }.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ];

                $response = $this->groqService->chat($messages, [
                    'temperature' => 0.8,
                    'max_tokens' => 6000
                ]);

                if (!$response) {
                    $this->logger->error('No response from Groq API for exercise generation');
                    if ($attempt < $maxRetries) {
                        $this->logger->info("Retrying... Attempt $attempt/$maxRetries");
                        sleep(1); // Wait 1 second before retry
                        continue;
                    }
                    throw new \Exception('Aucune réponse de l\'IA. Veuillez réessayer.');
                }

                // Parser la réponse de l'IA
                $exercices = $this->parseAIResponse($response);
                
                if (empty($exercices)) {
                    $this->logger->error('Failed to parse AI response for exercises', [
                        'response' => substr($response, 0, 500),
                        'attempt' => $attempt
                    ]);
                    if ($attempt < $maxRetries) {
                        $this->logger->info("Retrying... Attempt $attempt/$maxRetries");
                        sleep(1);
                        continue;
                    }
                    throw new \Exception('Impossible de générer les exercices. Format de réponse invalide.');
                }

                return $exercices;

            } catch (\Exception $e) {
                $this->logger->error('Error in exercise generation', [
                    'message' => $e->getMessage(),
                    'attempt' => $attempt,
                    'trace' => $e->getTraceAsString()
                ]);
                
                if ($attempt >= $maxRetries) {
                    throw $e;
                }
                
                sleep(1); // Wait before retry
            }
        }
        
        throw new \Exception('Impossible de générer les exercices après plusieurs tentatives.');
    }

    /**
     * Construit le prompt pour la génération d'exercices
     */
    private function buildGenerationPrompt(string $sujet, string $niveau, int $nombre): string
    {
        $pointsRange = match($niveau) {
            'Débutant' => '5-10',
            'Intermédiaire' => '10-15',
            'Avancé' => '15-20',
            default => '10-15'
        };

        return <<<PROMPT
Génère {$nombre} exercices sur "{$sujet}" (niveau {$niveau}).

RÈGLES RÉPONSES:
- Minimum 3-4 phrases complètes (120-250 caractères)
- Explication détaillée avec exemples concrets
- Jamais de réponses d'un seul mot ou phrase courte

EXEMPLE BONNE RÉPONSE:
"Une variable en Python est un conteneur qui stocke une valeur en mémoire sous un nom symbolique. Elle est créée lors de l'affectation avec =. Python utilise un typage dynamique, le type est déterminé automatiquement. Par exemple, x=5 crée un entier, nom='Alice' crée une chaîne."

FORMAT JSON (réponds UNIQUEMENT avec ce JSON, sans texte avant/après):
{
    "exercices": [
        {
            "question": "Question claire",
            "reponse": "Réponse détaillée 3-4 phrases minimum avec explication et exemple",
            "points": {$pointsRange}
        }
    ]
}

Génère EXACTEMENT {$nombre} exercices. Commence par { et termine par }.
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
        
        // Essayer d'extraire le JSON s'il y a du texte avant/après
        if (preg_match('/\{[\s\S]*"exercices"[\s\S]*\}/i', $response, $matches)) {
            $response = $matches[0];
        }
        
        // Tenter de réparer le JSON tronqué
        $response = $this->repairTruncatedJson($response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('JSON decode error in exercise generation', [
                'error' => json_last_error_msg(),
                'response' => substr($response, 0, 500)
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
            
            // Vérifier que la réponse n'est pas trop courte
            $reponse = trim($exercice['reponse']);
            $reponseLength = strlen($reponse);
            
            if ($reponseLength < 80) {
                $this->logger->warning('Skipping exercise with too short answer', [
                    'question' => $exercice['question'],
                    'reponse' => $reponse,
                    'length' => $reponseLength
                ]);
                continue;
            }

            $exercices[] = [
                'question' => trim($exercice['question']),
                'reponse' => $reponse,
                'points' => max(1, min(100, (int) $exercice['points']))
            ];
        }

        return $exercices;
    }
    
    /**
     * Tente de réparer un JSON tronqué
     */
    private function repairTruncatedJson(string $json): string
    {
        // Si le JSON se termine correctement, pas besoin de réparer
        if (substr(rtrim($json), -1) === '}') {
            return $json;
        }
        
        // Compter les accolades ouvrantes et fermantes
        $openBraces = substr_count($json, '{');
        $closeBraces = substr_count($json, '}');
        
        // Compter les crochets ouvrants et fermants
        $openBrackets = substr_count($json, '[');
        $closeBrackets = substr_count($json, ']');
        
        // Compter les guillemets (pour détecter les chaînes non fermées)
        $quotes = substr_count($json, '"');
        
        // Si une chaîne est ouverte, la fermer
        if ($quotes % 2 !== 0) {
            $json .= '"';
        }
        
        // Fermer les crochets manquants
        while ($closeBrackets < $openBrackets) {
            $json .= ']';
            $closeBrackets++;
        }
        
        // Fermer les accolades manquantes
        while ($closeBraces < $openBraces) {
            $json .= '}';
            $closeBraces++;
        }
        
        return $json;
    }
}
