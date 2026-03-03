<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SentimentAnalysisService
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
     * Analyse le sentiment d'un commentaire
     * @return array ['sentiment' => 'positive'|'negative'|'neutral', 'score' => float, 'explanation' => string]
     */
    public function analyzeSentiment(string $text): array
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
                            'content' => 'Tu es un expert en analyse de sentiment. Analyse le sentiment du texte fourni et réponds UNIQUEMENT avec un JSON au format: {"sentiment": "positive|negative|neutral", "score": 0.0-1.0, "explanation": "courte explication"}. Ne réponds qu\'avec le JSON, rien d\'autre.'
                        ],
                        [
                            'role' => 'user',
                            'content' => 'Analyse le sentiment de ce commentaire: ' . $text
                        ]
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 200,
                ],
            ]);

            $data = $response->toArray();
            $content = $data['choices'][0]['message']['content'] ?? '';
            
            // Extraire le JSON de la réponse
            $jsonMatch = [];
            if (preg_match('/\{[^}]+\}/', $content, $jsonMatch)) {
                $result = json_decode($jsonMatch[0], true);
                
                if ($result && isset($result['sentiment'])) {
                    return [
                        'sentiment' => $result['sentiment'],
                        'score' => $result['score'] ?? 0.5,
                        'explanation' => $result['explanation'] ?? ''
                    ];
                }
            }

            // Fallback si le parsing échoue
            return [
                'sentiment' => 'neutral',
                'score' => 0.5,
                'explanation' => 'Analyse non disponible'
            ];

        } catch (\Exception $e) {
            // En cas d'erreur, retourner un sentiment neutre
            return [
                'sentiment' => 'neutral',
                'score' => 0.5,
                'explanation' => 'Erreur d\'analyse: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Analyse plusieurs commentaires en batch
     * @param array $comments Tableau de textes
     * @return array Tableau de résultats d'analyse
     */
    public function analyzeBatch(array $comments): array
    {
        $results = [];
        foreach ($comments as $index => $comment) {
            $results[$index] = $this->analyzeSentiment($comment);
        }
        return $results;
    }

    /**
     * Obtient une couleur CSS basée sur le sentiment
     */
    public function getSentimentColor(string $sentiment): string
    {
        return match($sentiment) {
            'positive' => '#28a745',
            'negative' => '#dc3545',
            'neutral' => '#6c757d',
            default => '#6c757d'
        };
    }

    /**
     * Obtient une icône basée sur le sentiment
     */
    public function getSentimentIcon(string $sentiment): string
    {
        return match($sentiment) {
            'positive' => '😊',
            'negative' => '😞',
            'neutral' => '😐',
            default => '😐'
        };
    }
}
