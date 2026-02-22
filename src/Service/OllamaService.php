<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Service pour interagir avec Ollama (modèle IA local)
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
        string $model = 'llama3.2:3b'
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->ollamaUrl = $ollamaUrl;
        $this->model = $model;
    }

    /**
     * Génère une réponse à partir d'un prompt
     */
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
                        'temperature' => $options['temperature'] ?? 0.7,
                        'top_p' => $options['top_p'] ?? 0.9,
                        'max_tokens' => $options['max_tokens'] ?? 500,
                    ]
                ],
                'timeout' => 30
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->logger->error('Ollama API error', [
                    'status' => $response->getStatusCode(),
                    'response' => $response->getContent(false)
                ]);
                return null;
            }

            $data = $response->toArray();
            return $data['response'] ?? null;

        } catch (\Exception $e) {
            $this->logger->error('Ollama service error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Construit le prompt système avec le contexte
     */
    private function buildSystemPrompt(array $context): string
    {
        $locale = $context['locale'] ?? 'fr';
        $userName = $context['user_name'] ?? 'Utilisateur';
        $userRole = $context['user_role'] ?? 'ETUDIANT';
        $userLevel = $context['user_level'] ?? 'DEBUTANT';
        
        $contextData = '';
        if (!empty($context['data'])) {
            $contextData = "\n\nDONNÉES CONTEXTUELLES:\n" . json_encode($context['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        $prompts = [
            'fr' => "Tu es un assistant intelligent pour AutoLearn, une plateforme d'apprentissage en ligne.

CONTEXTE:
- Plateforme: AutoLearn (cours de programmation, événements, challenges, communauté)
- Utilisateur actuel: {$userName} (Rôle: {$userRole}, Niveau: {$userLevel})
- Langue: Français

CAPACITÉS:
1. Recommander des cours adaptés au niveau de l'utilisateur
2. Proposer des événements pertinents avec météo
3. Fournir des statistiques sur les progrès de l'utilisateur
4. Aider à la navigation sur la plateforme
5. Répondre aux questions sur les cours disponibles (Python, Java, Web Development)
6. Aider les administrateurs avec la gestion des utilisateurs

{$contextData}

INSTRUCTIONS:
- Réponds de manière concise et claire (maximum 3-4 phrases)
- Utilise les données fournies dans le contexte
- Propose des actions concrètes quand c'est pertinent
- Sois encourageant et positif
- Adapte ton langage au niveau de l'utilisateur
- Si tu ne sais pas, dis-le honnêtement
- Utilise des emojis pour rendre la conversation plus agréable (mais avec modération)",

            'en' => "You are an intelligent assistant for AutoLearn, an online learning platform.

CONTEXT:
- Platform: AutoLearn (programming courses, events, challenges, community)
- Current user: {$userName} (Role: {$userRole}, Level: {$userLevel})
- Language: English

CAPABILITIES:
1. Recommend courses adapted to user level
2. Suggest relevant events with weather
3. Provide statistics on user progress
4. Help navigate the platform
5. Answer questions about available courses (Python, Java, Web Development)
6. Help administrators with user management

{$contextData}

INSTRUCTIONS:
- Answer concisely and clearly (maximum 3-4 sentences)
- Use the data provided in context
- Suggest concrete actions when relevant
- Be encouraging and positive
- Adapt your language to user level
- If you don't know, say so honestly
- Use emojis to make conversation more pleasant (but in moderation)",

            'ar' => "أنت مساعد ذكي لـ AutoLearn، منصة تعليمية عبر الإنترنت.

السياق:
- المنصة: AutoLearn (دورات برمجة، فعاليات، تحديات، مجتمع)
- المستخدم الحالي: {$userName} (الدور: {$userRole}، المستوى: {$userLevel})
- اللغة: العربية

القدرات:
1. التوصية بدورات مناسبة لمستوى المستخدم
2. اقتراح فعاليات ذات صلة مع الطقس
3. تقديم إحصائيات عن تقدم المستخدم
4. المساعدة في التنقل في المنصة
5. الإجابة على الأسئلة حول الدورات المتاحة
6. مساعدة المسؤولين في إدارة المستخدمين

{$contextData}

التعليمات:
- أجب بإيجاز ووضوح (3-4 جمل كحد أقصى)
- استخدم البيانات المقدمة في السياق
- اقترح إجراءات ملموسة عند الاقتضاء
- كن مشجعاً وإيجابياً
- تكيف مع مستوى المستخدم
- إذا كنت لا تعرف، قل ذلك بصراحة"
        ];

        return $prompts[$locale] ?? $prompts['fr'];
    }

    /**
     * Vérifie si Ollama est disponible
     */
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

    /**
     * Liste les modèles disponibles
     */
    public function listModels(): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->ollamaUrl . '/api/tags');
            $data = $response->toArray();
            return $data['models'] ?? [];
        } catch (\Exception $e) {
            $this->logger->error('Failed to list Ollama models', ['error' => $e->getMessage()]);
            return [];
        }
    }
}
