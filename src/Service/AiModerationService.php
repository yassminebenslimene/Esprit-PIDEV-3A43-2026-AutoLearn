<?php

namespace App\Service;

class AiModerationService
{
    private array $badWords = [
        'idiot',
        'stupid',
        'hate',
        'insulte',
        'null'
    ];

    public function isClean(string $text): bool
    {
        foreach ($this->badWords as $word) {
            if (stripos($text, $word) !== false) {
                return false;
            }
        }

        return true;
    }
}