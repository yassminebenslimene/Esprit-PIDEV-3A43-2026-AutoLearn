<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AiSummaryService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $apiUrl;
    private string $model;

    public function __construct(
        HttpClientInterface $httpClient, 
        string $groqApiKey, 
        string $groqApiUrl,
        string $groqModel
    ) {
        $this->httpClient = $httpClient;
        $this->apiKey = $groqApiKey;
        $this->apiUrl = $groqApiUrl;
        $this->model = $groqModel;
    }

    public function generateSummary(string $content): ?string
    {
        try {
            // Limiter le contenu à 2000 caractères pour éviter les coûts élevés
            $truncatedContent = mb_substr(strip_tags($content), 0, 2000);

            $response = $this->httpClient->request('POST', $this->apiUrl . '/openai/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,  // Utiliser le modèle configuré
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un assistant qui génère des résumés courts et concis en français. Limite ton résumé à 2-3 phrases maximum.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Résume ce texte en 2-3 phrases maximum:\n\n" . $truncatedContent
                        ]
                    ],
                    'temperature' => 0.5,
                    'max_tokens' => 150,
                ],
            ]);

            $data = $response->toArray();
            
            if (isset($data['choices'][0]['message']['content'])) {
                return trim($data['choices'][0]['message']['content']);
            }

            return null;
        } catch (\Exception $e) {
            // En cas d'erreur, retourner null (le résumé sera optionnel)
            return null;
        }
    }
}