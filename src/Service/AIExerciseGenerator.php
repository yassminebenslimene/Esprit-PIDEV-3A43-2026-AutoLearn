<?php
// src/Service/AIExerciseGenerator.php

namespace App\Service;

class AIExerciseGenerator
{
    // Pas de dépendance à OpenAI pour le moment
    public function __construct()
    {
    }

    public function generateExercise(string $theme, string $niveau): array
    {
        return [
            'question' => "Question sur le thème '$theme' (niveau $niveau) : Qu'est-ce que le " . $theme . " ?",
            'reponse' => "C'est un concept important en " . $theme,
            'points' => 5,
            'type' => 'open'
        ];
    }

    public function generateQCM(string $theme, string $niveau): array
    {
        return [
            'question' => "QCM sur le thème '$theme' (niveau $niveau) : Quelle est la meilleure pratique ?",
            'options' => ["Option 1", "Option 2", "Option 3", "Option 4"],
            'bonneReponse' => 0,
            'points' => 5,
            'type' => 'qcm'
        ];
    }

    public function generateMultipleExercises(string $theme, string $niveau, int $count): array
    {
        $exercises = [];
        for ($i = 0; $i < $count; $i++) {
            $exercises[] = $this->generateExercise($theme, $niveau);
        }
        return $exercises;
    }
}