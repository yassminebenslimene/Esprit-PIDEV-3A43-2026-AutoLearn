<?php

namespace App\Utils;

class LikeEscaper
{
    /**
     * Escape special LIKE characters (% and _) to prevent SQL injection
     * and unexpected pattern matching behavior.
     */
    public static function escape(string $value): string
    {
        return addcslashes($value, '%_');
    }

    /**
     * Escape and wrap with wildcards for LIKE pattern matching.
     */
    public static function escapeAndWrap(string $value): string
    {
        return '%' . self::escape($value) . '%';
    }
}
