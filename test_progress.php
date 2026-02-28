<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->bootEnv(__DIR__.'/.env');

// Bootstrap Symfony kernel
$kernel = new \App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();

// Get services
$em = $container->get('doctrine')->getManager();
$progressService = $container->get(\App\Service\CourseProgressService::class);

// Get user ID 3
$user = $em->getRepository(\App\Entity\User::class)->find(3);
if (!$user) {
    echo "User not found!\n";
    exit(1);
}

echo "User: " . $user->getEmail() . "\n\n";

// Get all courses
$courses = $em->getRepository(\App\Entity\GestionDeCours\Cours::class)->findAll();

foreach ($courses as $cours) {
    echo "Cours: " . $cours->getTitre() . "\n";
    $stats = $progressService->getCourseProgressStats($user, $cours);
    echo "  - Chapitres complétés: " . $stats['completed_chapters'] . "/" . $stats['total_chapters'] . "\n";
    echo "  - Pourcentage: " . $stats['percentage'] . "%\n";
    echo "\n";
}
