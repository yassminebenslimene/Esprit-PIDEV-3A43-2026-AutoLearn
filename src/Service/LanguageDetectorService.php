<?php

namespace App\Service;

class LanguageDetectorService
{
    // Mots-clés français communs
    private const FRENCH_KEYWORDS = [
        'je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles',
        'le', 'la', 'les', 'un', 'une', 'des',
        'est', 'sont', 'être', 'avoir', 'faire',
        'quoi', 'quel', 'quelle', 'comment', 'pourquoi', 'où',
        'bonjour', 'merci', 'salut', 'oui', 'non',
        'cours', 'exercice', 'étudiant', 'apprendre',
    ];

    // Mots-clés anglais communs
    private const ENGLISH_KEYWORDS = [
        'i', 'you', 'he', 'she', 'we', 'they',
        'the', 'a', 'an', 'is', 'are', 'am',
        'what', 'which', 'how', 'why', 'where', 'when',
        'hello', 'thanks', 'yes', 'no', 'please',
        'course', 'exercise', 'student', 'learn',
        'show', 'me', 'my', 'get', 'want', 'need',
    ];

    /**
     * Détecte la langue du texte
     * 
     * @return string 'fr', 'en', ou 'other'
     */
    public function detect(string $text): string
    {
        $text = mb_strtolower($text);
        $words = preg_split('/\s+/', $text);
        
        $frenchScore = 0;
        $englishScore = 0;

        foreach ($words as $word) {
            // Nettoyer le mot (enlever ponctuation)
            $word = preg_replace('/[^\p{L}\p{N}]/u', '', $word);
            
            if (empty($word)) {
                continue;
            }

            // Compter les mots français
            if (in_array($word, self::FRENCH_KEYWORDS)) {
                $frenchScore++;
            }

            // Compter les mots anglais
            if (in_array($word, self::ENGLISH_KEYWORDS)) {
                $englishScore++;
            }

            // Détection par caractères spéciaux français
            if (preg_match('/[àâäéèêëïîôùûüÿçœæ]/u', $word)) {
                $frenchScore += 2; // Bonus pour accents français
            }
        }

        // Si aucun score, essayer détection par patterns
        if ($frenchScore === 0 && $englishScore === 0) {
            return $this->detectByPatterns($text);
        }

        // Retourner la langue avec le score le plus élevé
        if ($frenchScore > $englishScore) {
            return 'fr';
        } elseif ($englishScore > $frenchScore) {
            return 'en';
        }

        // Par défaut, considérer comme français si égalité
        return 'fr';
    }

    /**
     * Détection par patterns de phrases
     */
    private function detectByPatterns(string $text): string
    {
        // Patterns français
        $frenchPatterns = [
            '/\bje\s+(suis|veux|peux|dois)\b/i',
            '/\bc\'est\b/i',
            '/\bqu\'est-ce\b/i',
            '/\bpourquoi\s+\w+/i',
            '/\bcomment\s+\w+/i',
        ];

        // Patterns anglais
        $englishPatterns = [
            '/\bi\s+(am|want|can|need)\b/i',
            '/\bit\'s\b/i',
            '/\bwhat\s+is\b/i',
            '/\bhow\s+to\b/i',
            '/\bcan\s+you\b/i',
        ];

        foreach ($frenchPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return 'fr';
            }
        }

        foreach ($englishPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return 'en';
            }
        }

        // Si aucun pattern ne correspond, vérifier les caractères
        // Si contient des caractères non-latins, c'est "other"
        if (preg_match('/[\x{0600}-\x{06FF}\x{4E00}-\x{9FFF}\x{0400}-\x{04FF}]/u', $text)) {
            return 'other';
        }

        // Par défaut français
        return 'fr';
    }

    /**
     * Vérifie si la langue est supportée
     */
    public function isSupported(string $language): bool
    {
        return in_array($language, ['fr', 'en']);
    }

    /**
     * Retourne le message de refus selon la langue détectée
     */
    public function getUnsupportedLanguageMessage(string $detectedLanguage): string
    {
        // Message bilingue FR + EN
        return "Désolé, je ne comprends que le français et l'anglais. Pouvez-vous reformuler votre question en français ou en anglais?\n\n"
             . "Sorry, I only understand French and English. Can you rephrase your question in French or English?";
    }
}
