<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TitleSuggestionService
{
    private HttpClientInterface $httpClient;
    private string $groqApiKey;
    private string $groqApiUrl;
    private string $groqModel;

    public function __construct(
        HttpClientInterface $httpClient,
        string $groqApiKey,
        string $groqApiUrl,
        string $groqModel
    ) {
        $this->httpClient = $httpClient;
        $this->groqApiKey = $groqApiKey;
        $this->groqApiUrl = $groqApiUrl;
        $this->groqModel = $groqModel;
    }

    /**
     * Suggère un titre basé sur le contenu
     * @param string $content Le contenu du post ou de la communauté
     * @param int $maxLength Longueur maximale du titre
     * @return array ['title' => string, 'alternatives' => array]
     */
    public function suggestTitle(string $content, int $maxLength = 60): array
    {
        try {
            $response = $this->httpClient->request('POST', $this->groqApiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->groqApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->groqModel,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un expert en création de titres accrocheurs et pertinents. Génère 3 suggestions de titres courts et percutants (maximum ' . $maxLength . ' caractères) basés sur le contenu fourni. Réponds UNIQUEMENT avec un JSON au format: {"main": "titre principal", "alternatives": ["titre 2", "titre 3"]}. Ne réponds qu\'avec le JSON, rien d\'autre.'
                        ],
                        [
                            'role' => 'user',
                            'content' => 'Génère des titres pour ce contenu: ' . substr($content, 0, 500)
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 200,
                ],
            ]);

            $data = $response->toArray();
            $responseContent = $data['choices'][0]['message']['content'] ?? '';
            
            // Extraire le JSON de la réponse
            $jsonMatch = [];
            if (preg_match('/\{[^}]+\}/', $responseContent, $jsonMatch)) {
                $result = json_decode($jsonMatch[0], true);
                
                if ($result && isset($result['main'])) {
                    return [
                        'title' => $result['main'],
                        'alternatives' => $result['alternatives'] ?? []
                    ];
                }
            }

            // Fallback : générer un titre simple
            return $this->generateFallbackTitle($content, $maxLength);

        } catch (\Exception $e) {
            return $this->generateFallbackTitle($content, $maxLength);
        }
    }

    /**
     * Génère un titre de secours si l'API échoue
     */
    private function generateFallbackTitle(string $content, int $maxLength): array
    {
        // Prendre les premiers mots du contenu
        $words = explode(' ', strip_tags($content));
        $title = implode(' ', array_slice($words, 0, 8));
        
        if (strlen($title) > $maxLength) {
            $title = substr($title, 0, $maxLength - 3) . '...';
        }
        
        return [
            'title' => $title ?: 'Sans titre',
            'alternatives' => []
        ];
    }

    /**
     * Suggère un titre pour un post
     */
    public function suggestPostTitle(string $content): array
    {
        return $this->suggestTitle($content, 60);
    }

    /**
     * Suggère un titre pour une communauté
     */
    public function suggestCommunityTitle(string $description): array
    {
        return $this->suggestTitle($description, 50);
    }

    /**
     * Améliore un titre existant
     */
    public function improveTitle(string $currentTitle, string $content): array
    {
        try {
            $response = $this->httpClient->request('POST', $this->groqApiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->groqApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->groqModel,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un expert en amélioration de titres. Améliore le titre fourni pour le rendre plus accrocheur et pertinent. Réponds UNIQUEMENT avec un JSON au format: {"improved": "titre amélioré", "reason": "raison de l\'amélioration"}.'
                        ],
                        [
                            'role' => 'user',
                            'content' => 'Titre actuel: "' . $currentTitle . '". Contenu: ' . substr($content, 0, 300)
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 150,
                ],
            ]);

            $data = $response->toArray();
            $responseContent = $data['choices'][0]['message']['content'] ?? '';
            
            $jsonMatch = [];
            if (preg_match('/\{[^}]+\}/', $responseContent, $jsonMatch)) {
                $result = json_decode($jsonMatch[0], true);
                
                if ($result && isset($result['improved'])) {
                    return [
                        'title' => $result['improved'],
                        'reason' => $result['reason'] ?? ''
                    ];
                }
            }

            return ['title' => $currentTitle, 'reason' => ''];

        } catch (\Exception $e) {
            return ['title' => $currentTitle, 'reason' => ''];
        }
    }
}
