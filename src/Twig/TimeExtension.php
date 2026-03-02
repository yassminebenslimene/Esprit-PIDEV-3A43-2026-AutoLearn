<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_ago', [$this, 'timeAgo']),
        ];
    }

    public function timeAgo(\DateTimeInterface $date): string
    {
        $now = new \DateTime();
        $diff = $now->getTimestamp() - $date->getTimestamp();

        if ($diff < 60) {
            return 'il y a quelques secondes';
        }

        if ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes == 1 ? 'il y a 1 minute' : "il y a {$minutes} minutes";
        }

        if ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours == 1 ? 'il y a 1 heure' : "il y a {$hours} heures";
        }

        if ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days == 1 ? 'il y a 1 jour' : "il y a {$days} jours";
        }

        if ($diff < 2592000) {
            $weeks = floor($diff / 604800);
            return $weeks == 1 ? 'il y a 1 semaine' : "il y a {$weeks} semaines";
        }

        if ($diff < 31536000) {
            $months = floor($diff / 2592000);
            return $months == 1 ? 'il y a 1 mois' : "il y a {$months} mois";
        }

        $years = floor($diff / 31536000);
        return $years == 1 ? 'il y a 1 an' : "il y a {$years} ans";
    }
}
