<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class GroqService
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private string $apiKey;
    private string $apiUrl;
    private string $model;

    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        string $groqApiKey,
        string $groqApiUrl,
        string $groqModel
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->apiKey = $groqApiKey;
        $this->apiUrl = $groqApiUrl;
        $this->model = $groqModel;
    }

    /**
     * Envoie une requête à Groq API
     */
    public function chat(array $messages, array $options = []): ?string
    {
        try {
            $payload = [
                'model' => $options['model'] ?? $this->model,
                'messages' => $messages,
                'temperature' => $options['temperature'] ?? 0.7,
                'max_tokens' => $options['max_tokens'] ?? 1000,
                'top_p' => $options['top_p'] ?? 1,
                'stream' => false,
            ];

            $this->logger->info('Groq API Request', [
                'model' => $payload['model'],
                'messages_count' => count($messages),
            ]);

            $response = $this->httpClient->request('POST', $this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 30,
            ]);

            $statusCode = $response->getStatusCode();
            
            if ($statusCode !== 200) {
                $errorContent = $response->getContent(false);
                $this->logger->error('Groq API Error', [
                    'status_code' => $statusCode,
                    'response' => $errorContent,
                ]);
                
                // Check for rate limit error
                if ($statusCode === 429) {
                    $this->logger->error('Groq API Rate Limit Exceeded');
                }
                
                return null;
            }

            $data = $response->toArray();
            
            if (!isset($data['choices'][0]['message']['content'])) {
                $this->logger->error('Invalid Groq API Response', ['data' => $data]);
                return null;
            }

            $content = $data['choices'][0]['message']['content'];
            
            $this->logger->info('Groq API Response Success', [
                'response_length' => strlen($content),
                'tokens_used' => $data['usage']['total_tokens'] ?? 'unknown',
            ]);

            return $content;

        } catch (\Exception $e) {
            $this->logger->error('Groq API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Génère une réponse simple
     */
    public function generate(string $prompt, array $options = []): ?string
    {
        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        return $this->chat($messages, $options);
    }

    /**
     * Vérifie si l'API Groq est accessible
     */
    public function isAvailable(): bool
    {
        // Vérifier simplement si la clé API est configurée
        $available = !empty($this->apiKey) && $this->apiKey !== 'your_groq_api_key_here';
        
        $this->logger->info('Groq isAvailable check', [
            'available' => $available,
            'apiKey_length' => strlen($this->apiKey),
            'apiKey_first_chars' => substr($this->apiKey, 0, 10)
        ]);
        
        return $available;
    }
}
