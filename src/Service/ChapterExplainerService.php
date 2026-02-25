<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChapterExplainerService
{
    private const MODEL = 'meta-llama/llama-4-scout-17b-16e-instruct';
    private const API_URL = 'https://api.groq.com/openai/v1/chat/completions';
    
    private string $apiKey;
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        string $groqApiKey
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->apiKey = $groqApiKey;
    }

    /**
     * Génère une explication personnalisée d'un chapitre
     * 
     * @param string $chapterContent Contenu du chapitre
     * @param string $level Niveau: 'beginner' ou 'advanced'
     * @return array ['summary' => string, 'explanation' => string, 'keyPoints' => array]
     */
    public function explainChapter(string $chapterContent, string $level = 'beginner'): array
    {
        $this->logger->info('Generating chapter explanation', [
            'level' => $level,
            'content_length' => strlen($chapterContent)
        ]);

        $prompt = $this->buildPrompt($chapterContent, $level);

        try {
            $response = $this->httpClient->request('POST', self::API_URL, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => self::MODEL,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un assistant pédagogique expert qui explique des concepts de manière claire et structurée.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ],
                'timeout' => 30
            ]);

            $data = $response->toArray();
            $content = $data['choices'][0]['message']['content'] ?? '';

            $this->logger->info('Chapter explanation generated successfully');

            return $this->parseResponse($content);

        } catch (\Exception $e) {
            $this->logger->error('Error generating chapter explanation', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'summary' => 'Erreur lors de la génération du résumé.',
                'explanation' => 'Une erreur est survenue. Veuillez réessayer.',
                'keyPoints' => ['Erreur de connexion à l\'API']
            ];
        }
    }

    /**
     * Construit le prompt selon le niveau
     */
    private function buildPrompt(string $content, string $level): string
    {
        $levelInstruction = $level === 'advanced' 
            ? 'Utilise un vocabulaire technique et des explications approfondies.'
            : 'Utilise un langage simple et des exemples concrets pour les débutants.';

        return <<<PROMPT
Analyse ce chapitre de cours et fournis une explication structurée.

CHAPITRE:
$content

INSTRUCTIONS:
$levelInstruction

Réponds au format suivant (respecte exactement ce format):

RÉSUMÉ:
[Un résumé en 2-3 phrases du chapitre]

EXPLICATION:
[Une explication détaillée et pédagogique du contenu, avec des exemples si pertinent]

POINTS CLÉS:
- [Point clé 1]
- [Point clé 2]
- [Point clé 3]
- [Point clé 4]
- [Point clé 5]

Fournis une réponse complète et structurée.
PROMPT;
    }

    /**
     * Parse la réponse de l'IA
     */
    private function parseResponse(string $content): array
    {
        $summary = '';
        $explanation = '';
        $keyPoints = [];

        // Extraire le résumé
        if (preg_match('/RÉSUMÉ:\s*\n(.*?)\n\n/s', $content, $matches)) {
            $summary = trim($matches[1]);
        }

        // Extraire l'explication
        if (preg_match('/EXPLICATION:\s*\n(.*?)\n\nPOINTS CLÉS:/s', $content, $matches)) {
            $explanation = trim($matches[1]);
        }

        // Extraire les points clés
        if (preg_match('/POINTS CLÉS:\s*\n(.*?)$/s', $content, $matches)) {
            $pointsText = trim($matches[1]);
            $lines = explode("\n", $pointsText);
            foreach ($lines as $line) {
                $line = trim($line);
                if (preg_match('/^[-•]\s*(.+)$/', $line, $match)) {
                    $keyPoints[] = trim($match[1]);
                }
            }
        }

        // Fallback si le parsing échoue
        if (empty($summary) && empty($explanation)) {
            $parts = explode("\n\n", $content, 2);
            $summary = $parts[0] ?? $content;
            $explanation = $parts[1] ?? $content;
        }

        return [
            'summary' => $summary ?: 'Résumé non disponible',
            'explanation' => $explanation ?: $content,
            'keyPoints' => !empty($keyPoints) ? $keyPoints : ['Analyse du contenu en cours...']
        ];
    }
}
