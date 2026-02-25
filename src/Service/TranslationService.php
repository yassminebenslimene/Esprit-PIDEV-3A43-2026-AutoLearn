<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Service de traduction automatique avec Groq AI
 */
class TranslationService
{
    private const MODEL = 'meta-llama/llama-4-scout-17b-16e-instruct';
    private const API_URL = 'https://api.groq.com/openai/v1/chat/completions';
    
    private const LANGUAGES = [
        'fr' => 'Français',
        'en' => 'English',
        'es' => 'Español',
        'ar' => 'العربية',
        'de' => 'Deutsch',
        'it' => 'Italiano',
        'pt' => 'Português',
        'zh' => '中文',
    ];

    private string $apiKey;
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private CacheInterface $cache;

    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        CacheInterface $cache,
        string $groqApiKey
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->apiKey = $groqApiKey;
    }

    /**
     * Traduit un texte vers une langue cible
     * 
     * @param string $text Texte à traduire
     * @param string $targetLang Code de la langue cible (fr, en, es, ar, etc.)
     * @param string $sourceLang Code de la langue source (optionnel, auto-détecté si null)
     * @return string Texte traduit
     */
    public function translate(string $text, string $targetLang, ?string $sourceLang = null): string
    {
        // Générer une clé de cache unique
        $cacheKey = 'translation_' . md5($text . $targetLang . $sourceLang);

        // Vérifier le cache
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($text, $targetLang, $sourceLang) {
            $item->expiresAfter(86400 * 7); // Cache 7 jours

            $this->logger->info('Translating text', [
                'target_lang' => $targetLang,
                'source_lang' => $sourceLang,
                'text_length' => strlen($text)
            ]);

            $prompt = $this->buildTranslationPrompt($text, $targetLang, $sourceLang);

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
                                'content' => 'Tu es un traducteur professionnel. Tu traduis le texte de manière précise et naturelle, en préservant le formatage HTML si présent.'
                            ],
                            [
                                'role' => 'user',
                                'content' => $prompt
                            ]
                        ],
                        'temperature' => 0.3,
                        'max_tokens' => 4000,
                    ],
                    'timeout' => 30
                ]);

                $data = $response->toArray();
                $translatedText = $data['choices'][0]['message']['content'] ?? '';

                $this->logger->info('Translation successful');

                return $translatedText;

            } catch (\Exception $e) {
                $this->logger->error('Translation error', [
                    'error' => $e->getMessage()
                ]);
                
                return $text; // Retourner le texte original en cas d'erreur
            }
        });
    }

    /**
     * Traduit un chapitre complet
     * 
     * @param object $chapitre Entité Chapitre
     * @param string $targetLang Langue cible
     * @return array ['titre' => string, 'contenu' => string]
     */
    public function translateChapter($chapitre, string $targetLang): array
    {
        $titre = $chapitre->getTitre();
        $contenu = $chapitre->getContenu();

        return [
            'titre' => $this->translate($titre, $targetLang),
            'contenu' => $this->translate($contenu, $targetLang),
        ];
    }

    /**
     * Construit le prompt de traduction
     */
    private function buildTranslationPrompt(string $text, string $targetLang, ?string $sourceLang): string
    {
        $targetLanguageName = self::LANGUAGES[$targetLang] ?? $targetLang;
        
        if ($sourceLang) {
            $sourceLanguageName = self::LANGUAGES[$sourceLang] ?? $sourceLang;
            return "Traduis ce texte de {$sourceLanguageName} vers {$targetLanguageName}. Préserve le formatage HTML si présent. Ne traduis que le contenu, pas les balises HTML.\n\nTexte à traduire:\n{$text}";
        }

        return "Traduis ce texte vers {$targetLanguageName}. Préserve le formatage HTML si présent. Ne traduis que le contenu, pas les balises HTML.\n\nTexte à traduire:\n{$text}";
    }

    /**
     * Retourne la liste des langues supportées
     * 
     * @return array ['code' => 'Nom']
     */
    public function getSupportedLanguages(): array
    {
        return self::LANGUAGES;
    }

    /**
     * Vérifie si une langue est supportée
     */
    public function isLanguageSupported(string $langCode): bool
    {
        return isset(self::LANGUAGES[$langCode]);
    }
}
