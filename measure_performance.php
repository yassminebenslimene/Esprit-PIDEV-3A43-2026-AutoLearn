<?php
// Script de mesure de performance simple
// Usage: php measure_performance.php

require __DIR__.'/vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

// Charger les variables d'environnement
(new Dotenv())->bootEnv(__DIR__.'/.env');

// Créer le kernel
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();

echo "\n=== MESURE DE PERFORMANCE - BrainUp ===\n\n";

// Test 1: Compter les entités
echo "📊 Test 1: Comptage des entités\n";
$startTime = microtime(true);
$startMemory = memory_get_usage(true);

$em = $container->get('doctrine')->getManager();
$totalCours = $em->getRepository('App\Entity\GestionDeCours\Cours')->count([]);
$totalExercices = $em->getRepository('App\Entity\Exercice')->count([]);
$totalChallenges = $em->getRepository('App\Entity\Challenge')->count([]);
$totalUsers = $em->getRepository('App\Entity\User')->count([]);

$endTime = microtime(true);
$endMemory = memory_get_usage(true);

$time1 = round(($endTime - $startTime) * 1000, 2);
$memory1 = round(($endMemory - $startMemory) / 1024 / 1024, 2);

echo "  ⏱️  Temps: {$time1} ms\n";
echo "  💾 Mémoire: {$memory1} MB\n";
echo "  📚 Cours: {$totalCours}\n";
echo "  📝 Exercices: {$totalExercices}\n";
echo "  🎯 Challenges: {$totalChallenges}\n";
echo "  👥 Utilisateurs: {$totalUsers}\n\n";

// Test 2: Charger des cours avec pagination
echo "📊 Test 2: Chargement cours (page d'accueil)\n";
$startTime = microtime(true);
$startMemory = memory_get_usage(true);

$coursRepo = $em->getRepository('App\Entity\GestionDeCours\Cours');
$cours = $coursRepo->findAllPaginated(12, 0);

$endTime = microtime(true);
$endMemory = memory_get_usage(true);

$time2 = round(($endTime - $startTime) * 1000, 2);
$memory2 = round(($endMemory - $startMemory) / 1024 / 1024, 2);

echo "  ⏱️  Temps: {$time2} ms\n";
echo "  💾 Mémoire: {$memory2} MB\n";
echo "  📚 Cours chargés: " . count($cours) . "\n\n";

// Test 3: Pic mémoire global
$peakMemory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

echo "=== RÉSUMÉ POUR VOTRE RAPPORT ===\n\n";
echo "Tableau 4 - Indicateurs de performance:\n\n";
echo "| Indicateur | Valeur |\n";
echo "|------------|--------|\n";
echo "| Temps moyen de réponse page d'accueil (ms) | {$time2} ms |\n";
echo "| Temps d'exécution comptage entités (ms) | {$time1} ms |\n";
echo "| Utilisation mémoire page d'accueil | {$memory2} MB |\n";
echo "| Pic mémoire global | {$peakMemory} MB |\n\n";

echo "✅ Mesures terminées!\n";
echo "💡 Conseil: Prenez des captures d'écran du Symfony Profiler pour plus de détails.\n\n";

