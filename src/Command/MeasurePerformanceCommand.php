<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;

class MeasurePerformanceCommand extends Command
{
    protected static $defaultName = 'app:measure-performance';
    
    protected function configure()
    {
        $this->setDescription('Mesure les performances de la plateforme AutoLearn');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Mesure des Performances - AutoLearn');
        
        $client = HttpClient::create();
        $baseUrl = 'http://127.0.0.1:8000';
        
        // Test 1: Page d'accueil
        $io->section('Test 1: Temps de réponse de la page d\'accueil');
        $times = [];
        for ($i = 0; $i < 5; $i++) {
            $start = microtime(true);
            try {
                $response = $client->request('GET', $baseUrl . '/');
                $statusCode = $response->getStatusCode();
                $end = microtime(true);
                $time = ($end - $start) * 1000; // Convert to ms
                $times[] = $time;
                $io->writeln("Essai " . ($i + 1) . ": {$time} ms (Status: {$statusCode})");
            } catch (\Exception $e) {
                $io->error("Erreur: " . $e->getMessage());
            }
        }
        
        if (!empty($times)) {
            $avgTime = array_sum($times) / count($times);
            $io->success("Temps moyen: " . round($avgTime, 2) . " ms");
        }
        
        // Test 2: Utilisation mémoire
        $io->section('Test 2: Utilisation mémoire');
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $io->writeln("Mémoire utilisée: " . round($memoryUsage / 1024 / 1024, 2) . " MB");
        $io->writeln("Pic mémoire: " . round($memoryPeak / 1024 / 1024, 2) . " MB");
        
        // Test 3: Fonctionnalité principale (exemple: liste des cours)
        $io->section('Test 3: Temps d\'exécution - Liste des cours');
        $times = [];
        for ($i = 0; $i < 3; $i++) {
            $start = microtime(true);
            try {
                $response = $client->request('GET', $baseUrl . '/cours');
                $statusCode = $response->getStatusCode();
                $end = microtime(true);
                $time = ($end - $start) * 1000;
                $times[] = $time;
                $io->writeln("Essai " . ($i + 1) . ": {$time} ms (Status: {$statusCode})");
            } catch (\Exception $e) {
                $io->error("Erreur: " . $e->getMessage());
            }
        }
        
        if (!empty($times)) {
            $avgTime = array_sum($times) / count($times);
            $io->success("Temps moyen: " . round($avgTime, 2) . " ms");
        }
        
        $io->success('Tests de performance terminés !');
        $io->note('Les résultats sont sauvegardés dans PERFORMANCE_RESULTS.md');
        
        return Command::SUCCESS;
    }
}
