<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class CourseGeneratorService
{
    private string $groqApiKey;
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(
        string $groqApiKey,
        HttpClientInterface $httpClient,
        LoggerInterface $logger
    ) {
        $this->groqApiKey = $groqApiKey;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * Génère un chapitre basé sur le titre du cours
     */
    public function generateChapter(string $courseTitle, string $subject = '', string $level = ''): array
    {
        try {
            $prompt = $this->buildChapterPrompt($courseTitle, $subject, $level);
            
            $response = $this->httpClient->request('POST', 'https://api.groq.com/openai/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->groqApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un expert pédagogique qui crée des chapitres de cours structurés et détaillés. Tu réponds UNIQUEMENT en JSON valide.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ],
                'timeout' => 30,
            ]);

            $data = $response->toArray();
            
            if (!isset($data['choices'][0]['message']['content'])) {
                throw new \Exception('Réponse invalide de l\'API Groq');
            }

            $content = $data['choices'][0]['message']['content'];
            
            // Nettoyer le JSON (enlever les balises markdown si présentes)
            $content = preg_replace('/```json\s*/', '', $content);
            $content = preg_replace('/```\s*$/', '', $content);
            $content = trim($content);
            
            $chapterData = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Erreur de parsing JSON: ' . json_last_error_msg());
            }

            return [
                'success' => true,
                'chapter' => $chapterData
            ];

        } catch (\Exception $e) {
            $this->logger->error('Erreur génération chapitre: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function buildChapterPrompt(string $courseTitle, string $subject, string $level): string
    {
        $context = "Cours: $courseTitle";
        if ($subject) {
            $context .= "\nMatière: $subject";
        }
        if ($level) {
            $context .= "\nNiveau: $level";
        }

        return <<<PROMPT
Génère un chapitre de cours pour:
$context

Réponds UNIQUEMENT avec un objet JSON valide (sans balises markdown) avec cette structure exacte:
{
    "titre": "Titre du chapitre",
    "contenu": "Contenu détaillé du chapitre avec explications complètes (minimum 500 caractères)",
    "ressources": "Ressources et références utiles"
}

Le contenu doit être:
- Pédagogique et bien structuré
- Adapté au niveau du cours
- Complet avec des explications claires
- En français
- Minimum 500 caractères pour le contenu

Réponds UNIQUEMENT avec le JSON, sans texte avant ou après.
PROMPT;
    }
}
