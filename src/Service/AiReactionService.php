<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AiReactionService
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
     * Analyse le contenu et suggère des réactions appropriées
     * @param string $content Le contenu du post
     * @return array ['primary' => string, 'secondary' => array, 'reason' => string]
     */
    public function suggestReactions(string $content): array
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
                            'content' => 'Tu es un expert en analyse émotionnelle de contenu. Analyse le contenu et suggère les réactions les plus appropriées parmi: "utile" (👍), "inspirant" (❤️), "interessant" (🤔), "surprenant" (😮), "educatif" (📚), "drole" (😄). Réponds UNIQUEMENT avec un JSON au format: {"primary": "reaction_principale", "secondary": ["reaction2", "reaction3"], "reason": "courte explication"}. Ne réponds qu\'avec le JSON, rien d\'autre.'
                        ],
                        [
                            'role' => 'user',
                            'content' => 'Analyse ce contenu et suggère des réactions: ' . substr(strip_tags($content), 0, 500)
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
                
                if ($result && isset($result['primary'])) {
                    return [
                        'primary' => $result['primary'],
                        'secondary' => $result['secondary'] ?? [],
                        'reason' => $result['reason'] ?? ''
                    ];
                }
            }

            // Fallback
            return $this->getDefaultReaction();

        } catch (\Exception $e) {
            return $this->getDefaultReaction();
        }
    }

    /**
     * Réaction par défaut en cas d'erreur
     */
    private function getDefaultReaction(): array
    {
        return [
            'primary' => 'interessant',
            'secondary' => ['utile'],
            'reason' => 'Contenu intéressant'
        ];
    }

    /**
     * Obtenir l'emoji et le label pour une réaction
     */
    public function getReactionDisplay(string $reaction): array
    {
        $reactions = [
            'utile' => ['emoji' => '👍', 'label' => 'Utile', 'color' => '#4CAF50'],
            'inspirant' => ['emoji' => '❤️', 'label' => 'Inspirant', 'color' => '#E91E63'],
            'interessant' => ['emoji' => '🤔', 'label' => 'Intéressant', 'color' => '#FF9800'],
            'surprenant' => ['emoji' => '😮', 'label' => 'Surprenant', 'color' => '#9C27B0'],
            'educatif' => ['emoji' => '📚', 'label' => 'Éducatif', 'color' => '#2196F3'],
            'drole' => ['emoji' => '😄', 'label' => 'Drôle', 'color' => '#FFC107'],
        ];

        return $reactions[$reaction] ?? ['emoji' => '👍', 'label' => 'Intéressant', 'color' => '#667eea'];
    }
}
