<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Service Ollama ULTRA-SIMPLIFIÉ - Génération d'actions uniquement
 */
class OllamaService
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private string $ollamaUrl;
    private string $model;

    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        string $ollamaUrl = 'http://localhost:11434',
        string $model = 'llama3.2:1b'
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->ollamaUrl = $ollamaUrl;
        $this->model = $model;
    }

    public function generate(string $prompt, array $context = [], array $options = []): ?string
    {
        try {
            $systemPrompt = $this->buildSystemPrompt($context);
            
            $response = $this->httpClient->request('POST', $this->ollamaUrl . '/api/generate', [
                'json' => [
                    'model' => $options['model'] ?? $this->model,
                    'prompt' => $prompt,
                    'system' => $systemPrompt,
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.1, // Très bas pour cohérence
                        'top_p' => 0.9,
                        'num_predict' => 50, // Très court
                    ]
                ],
                'timeout' => 45
            ]);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $data = $response->toArray();
            $result = $data['response'] ?? null;
            
            // Nettoyer la réponse
            if ($result) {
                $lines = explode("\n", trim($result));
                $result = trim($lines[0]);
                
                // Si la réponse ressemble à une action mais manque ACTION:, l'ajouter
                if (preg_match('/^(create_student|suspend_user|unsuspend_user|get_inactive_users|create_team)/', $result)) {
                    if (!str_starts_with($result, 'ACTION:')) {
                        $result = 'ACTION:' . $result;
                    }
                }
                
                // Normaliser les séparateurs (= vers :)
                $result = str_replace('|reason=', '|reason:', $result);
                $result = str_replace('|user_id=', '|user_id:', $result);
            }
            
            return $result;

        } catch (\Exception $e) {
            $this->logger->error('Ollama error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function buildSystemPrompt(array $context): string
    {
        $userRole = $context['user_role'] ?? 'ETUDIANT';
        
        $actions = '';
        if ($userRole === 'ADMIN') {
            $actions = "ADMIN can do:
- create_student
- suspend_user
- get_inactive_users";
        } else {
            $actions = "STUDENT can do:
- create_team";
        }

        return "You are AutoLearn AI. Output format: ACTION:name|param:value

{$actions}

CRITICAL: Start with ACTION:

Examples:
Q: creer etudiant nom:Rami email:rami@mail.com
A: ACTION:create_student|nom:Rami|prenom:Rami|email:rami@mail.com|niveau:DEBUTANT

Q: suspend user id 10
A: ACTION:suspend_user|user_id:10|reason:Suspendu

Q: utilisateurs inactifs
A: ACTION:get_inactive_users|days:7

Q: bonjour
A: Salut! Je peux t'aider avec les étudiants, stats, etc.

REMEMBER: Always start with ACTION: for actions!";
    }

    public function isAvailable(): bool
    {
        try {
            $response = $this->httpClient->request('GET', $this->ollamaUrl . '/api/tags', [
                'timeout' => 5
            ]);
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function listModels(): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->ollamaUrl . '/api/tags');
            $data = $response->toArray();
            return $data['models'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }
}
