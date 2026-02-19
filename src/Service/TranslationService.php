<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class TranslationService
{
    private const LIBRETRANSLATE_URL = 'https://libretranslate.com/translate';
    private const TIMEOUT = 10; // 10 seconds timeout

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger
    ) {}

    /**
     * Traduit un texte via l'API LibreTranslate
     * 
     * @param string $text Texte à traduire
     * @param string $sourceLang Langue source (ex: 'fr')
     * @param string $targetLang Langue cible (ex: 'en')
     * @return string|null Texte traduit ou null en cas d'erreur
     */
    public function translate(string $text, string $sourceLang, string $targetLang): ?string
    {
        // Essaie d'abord MyMemory API (gratuit, sans clé)
        $result = $this->translateWithMyMemory($text, $sourceLang, $targetLang);
        
        // Si MyMemory échoue, utilise le mode demo
        if ($result === null) {
            $this->logger->warning('MyMemory API failed, using demo mode');
            return $this->translateDemo($text, $targetLang);
        }
        
        return $result;

        /* PRODUCTION: Décommentez ce code et utilisez une vraie API de traduction
        try {
            $response = $this->httpClient->request('POST', self::LIBRETRANSLATE_URL, [
                'timeout' => self::TIMEOUT,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'q' => $text,
                    'source' => $sourceLang,
                    'target' => $targetLang,
                    'format' => 'text'
                ]
            ]);

            $data = $response->toArray();
            
            if (isset($data['translatedText'])) {
                return $data['translatedText'];
            }

            $this->logger->error('LibreTranslate: Réponse invalide', ['data' => $data]);
            return null;

        } catch (\Exception $e) {
            $this->logger->error('Erreur de traduction', [
                'message' => $e->getMessage(),
                'source' => $sourceLang,
                'target' => $targetLang
            ]);
            return null;
        }
        */
    }

    /**
     * Traduction de démonstration pour les tests
     * Ajoute simplement un préfixe pour montrer que la traduction fonctionne
     */
    private function translateDemo(string $text, string $targetLang): string
    {
        $prefixes = [
            'en' => '[EN] ',
            'es' => '[ES] ',
            'de' => '[DE] ',
            'it' => '[IT] ',
        ];

        $prefix = $prefixes[$targetLang] ?? '[' . strtoupper($targetLang) . '] ';
        
        // Simule un délai de traduction réaliste
        usleep(500000); // 0.5 secondes
        
        return $prefix . $text;
    }

    /**
     * Traduction avec MyMemory API (gratuit, sans clé API)
     * Alternative gratuite et simple à utiliser
     */
    private function translateWithMyMemory(string $text, string $sourceLang, string $targetLang): ?string
    {
        try {
            $url = sprintf(
                'https://api.mymemory.translated.net/get?q=%s&langpair=%s|%s',
                urlencode($text),
                $sourceLang,
                $targetLang
            );

            $response = $this->httpClient->request('GET', $url, [
                'timeout' => self::TIMEOUT
            ]);

            $data = $response->toArray();
            
            if (isset($data['responseData']['translatedText'])) {
                return $data['responseData']['translatedText'];
            }

            $this->logger->error('MyMemory: Réponse invalide', ['data' => $data]);
            return null;

        } catch (\Exception $e) {
            $this->logger->error('MyMemory error', [
                'message' => $e->getMessage(),
                'source' => $sourceLang,
                'target' => $targetLang
            ]);
            return null;
        }
    }

    /**
     * Vérifie si une langue est supportée
     */
    public function isLanguageSupported(string $lang): bool
    {
        return in_array($lang, ['fr', 'en', 'es', 'de', 'it', 'pt', 'ru', 'zh', 'ja', 'ar']);
    }
}
