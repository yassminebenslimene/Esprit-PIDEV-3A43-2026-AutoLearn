<?php

namespace App\Enum;

enum SentimentFeedback: string
{
    case TRES_SATISFAIT = 'Très satisfait';
    case SATISFAIT = 'Satisfait';
    case NEUTRE = 'Neutre';
    case DECU = 'Déçu';
    case TRES_DECU = 'Très déçu';

    /**
     * Retourne l'emoji correspondant au sentiment
     */
    public function getEmoji(): string
    {
        return match($this) {
            self::TRES_SATISFAIT => '😍',
            self::SATISFAIT => '😊',
            self::NEUTRE => '😐',
            self::DECU => '😞',
            self::TRES_DECU => '😢',
        };
    }

    /**
     * Retourne la couleur CSS correspondante
     */
    public function getColor(): string
    {
        return match($this) {
            self::TRES_SATISFAIT => '#10b981', // Vert foncé
            self::SATISFAIT => '#34d399',      // Vert clair
            self::NEUTRE => '#fbbf24',         // Jaune
            self::DECU => '#f97316',           // Orange
            self::TRES_DECU => '#ef4444',      // Rouge
        };
    }

    /**
     * Retourne le score numérique (1-5) pour l'analyse
     */
    public function getScore(): int
    {
        return match($this) {
            self::TRES_SATISFAIT => 5,
            self::SATISFAIT => 4,
            self::NEUTRE => 3,
            self::DECU => 2,
            self::TRES_DECU => 1,
        };
    }

    /**
     * Crée un SentimentFeedback depuis un score (1-5)
     */
    public static function fromScore(int $score): self
    {
        return match($score) {
            5 => self::TRES_SATISFAIT,
            4 => self::SATISFAIT,
            3 => self::NEUTRE,
            2 => self::DECU,
            1 => self::TRES_DECU,
            default => self::NEUTRE,
        };
    }
}
