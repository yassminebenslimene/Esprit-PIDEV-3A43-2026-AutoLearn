<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Cours\CoursRepository;
use App\Repository\ExerciceRepository;
use App\Repository\ChallengeRepository;
use App\Repository\EvenementRepository;
use App\Service\CourseProgressService;

#[AsCommand(
    name: 'app:measure-performance',
    description: 'Mesure les performances de l\'application'
)]
class MeasurePerformanceCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private CoursRepository $coursRepository,
        private ExerciceRepository $exerciceRepository,
        private ChallengeRepository $challengeRepository,
        private EvenementRepository $evenementRepository,
        private CourseProgressService $progressService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('📊 MESURE DE PERFORMANCE - BrainUp');
        
        // Activer le query logger
        $sqlLogger = new \Doctrine\DBAL\Logging\DebugStack();
        $this->em->getConnection()->getConfiguration()->setSQLLogger($sqlLogger);
        
        $results = [];
        
        // Test 1: Page d'accueil (simulation)
        $io->section('Test 1: Chargement page d\'accueil');
        $result1 = $this->testHomePage($sqlLogger);
        $results['home'] = $result1;
        $this->displayResults($io, 'Page d\'accueil', $result1);
        
        // Reset logger
        $sqlLogger->queries = [];
        
        // Test 2: Liste des cours
        $io->section('Test 2: Liste des cours');
        $result2 = $this->testCoursList($sqlLogger);
        $results['cours'] = $result2;
        $this->displayResults($io, 'Liste cours', $result2);
        
        // Reset logger
        $sqlLogger->queries = [];
        
        // Test 3: Backoffice analytics
        $io->section('Test 3: Backoffice Analytics');
        $result3 = $this->testBackofficeAnalytics($sqlLogger);
        $results['analytics'] = $result3;
        $this->displayResults($io, 'Analytics', $result3);
        
        // Résumé final
        $io->section('📋 RÉSUMÉ POUR VOTRE RAPPORT');
        $io->table(
            ['Page', 'Temps (ms)', 'Mémoire (MB)', 'Requêtes SQL'],
            [
                ['Page d\'accueil', $result1['time'], $result1['memory'], $result1['queries']],
                ['Liste cours', $result2['time'], $result2['memory'], $result2['queries']],
                ['Analytics', $result3['time'], $result3['memory'], $result3['queries']],
            ]
        );
        
        $io->success('Mesures terminées! Utilisez ces valeurs pour votre rapport.');
        
        return Command::SUCCESS;
    }
    
    private function testHomePage($sqlLogger): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        // Simuler le chargement de la page d'accueil
        $cours = $this->coursRepository->findAllPaginated(12, 0);
        $challenges = $this->challengeRepository->createQueryBuilder('ch')
            ->setMaxResults(8)
            ->orderBy('ch.id', 'DESC')
            ->getQuery()
            ->getResult();
        $evenements = $this->evenementRepository->createQueryBuilder('e')
            ->setMaxResults(6)
            ->orderBy('e.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        
        return [
            'time' => round(($endTime - $startTime) * 1000, 2),
            'memory' => round(($endMemory - $startMemory) / 1024 / 1024, 2),
            'queries' => count($sqlLogger->queries)
        ];
    }
    
    private function testCoursList($sqlLogger): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        // Charger tous les cours avec pagination
        $cours = $this->coursRepository->findAllPaginated(50, 0);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        
        return [
            'time' => round(($endTime - $startTime) * 1000, 2),
            'memory' => round(($endMemory - $startMemory) / 1024 / 1024, 2),
            'queries' => count($sqlLogger->queries)
        ];
    }
    
    private function testBackofficeAnalytics($sqlLogger): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        // Simuler le chargement du dashboard analytics
        $totalCours = $this->coursRepository->count([]);
        $totalExercices = $this->exerciceRepository->count([]);
        $totalChallenges = $this->challengeRepository->count([]);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        
        return [
            'time' => round(($endTime - $startTime) * 1000, 2),
            'memory' => round(($endMemory - $startMemory) / 1024 / 1024, 2),
            'queries' => count($sqlLogger->queries)
        ];
    }
    
    private function displayResults(SymfonyStyle $io, string $test, array $result): void
    {
        $io->writeln([
            "⏱️  Temps d'exécution: <info>{$result['time']} ms</info>",
            "💾 Mémoire utilisée: <info>{$result['memory']} MB</info>",
            "🔍 Requêtes SQL: <info>{$result['queries']}</info>",
        ]);
    }
}
