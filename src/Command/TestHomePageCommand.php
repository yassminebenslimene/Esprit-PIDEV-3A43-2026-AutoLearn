<?php

namespace App\Command;

use App\Repository\ChallengeRepository;
use App\Repository\Cours\CoursRepository;
use App\Repository\EvenementRepository;
use App\Repository\EquipeRepository;
use App\Service\CourseProgressService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:test-homepage',
    description: 'Test homepage loading performance'
)]
class TestHomePageCommand extends Command
{
    public function __construct(
        private ChallengeRepository $challengeRepository,
        private EvenementRepository $evenementRepository,
        private EquipeRepository $equipeRepository,
        private CoursRepository $coursRepository,
        private CourseProgressService $progressService,
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Test Homepage Performance');

        // Test 1: Cours
        $io->section('Loading Cours...');
        $start = microtime(true);
        $cours = $this->coursRepository->createQueryBuilder('c')
            ->setMaxResults(12)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
        $time = round((microtime(true) - $start) * 1000, 2);
        $io->success("Loaded " . count($cours) . " cours in {$time}ms");

        // Test 2: Challenges
        $io->section('Loading Challenges...');
        $start = microtime(true);
        $challenges = $this->challengeRepository->createQueryBuilder('ch')
            ->setMaxResults(8)
            ->orderBy('ch.id', 'DESC')
            ->getQuery()
            ->getResult();
        $time = round((microtime(true) - $start) * 1000, 2);
        $io->success("Loaded " . count($challenges) . " challenges in {$time}ms");

        // Test 3: Evenements
        $io->section('Loading Evenements...');
        $start = microtime(true);
        $evenements = $this->evenementRepository->createQueryBuilder('e')
            ->setMaxResults(6)
            ->orderBy('e.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
        $time = round((microtime(true) - $start) * 1000, 2);
        $io->success("Loaded " . count($evenements) . " evenements in {$time}ms");

        // Test 4: Equipes
        $io->section('Loading Equipes...');
        $start = microtime(true);
        $equipes = $this->equipeRepository->createQueryBuilder('eq')
            ->setMaxResults(10)
            ->orderBy('eq.id', 'DESC')
            ->getQuery()
            ->getResult();
        $time = round((microtime(true) - $start) * 1000, 2);
        $io->success("Loaded " . count($equipes) . " equipes in {$time}ms");

        // Test 5: Progress Service (PROBLÈME POTENTIEL)
        $io->section('Testing Progress Service...');
        $user = $this->em->getRepository('App\Entity\User')->findOneBy([]);
        
        if ($user) {
            $io->writeln("Testing with user: " . $user->getEmail());
            $start = microtime(true);
            
            try {
                $coursProgress = $this->progressService->getAllCoursesProgress($user, $cours);
                $time = round((microtime(true) - $start) * 1000, 2);
                $io->success("Progress calculated in {$time}ms");
                
                if ($time > 1000) {
                    $io->warning("Progress service is SLOW! ({$time}ms)");
                }
            } catch (\Exception $e) {
                $io->error("Progress service failed: " . $e->getMessage());
                $io->writeln($e->getTraceAsString());
                return Command::FAILURE;
            }
        } else {
            $io->warning("No user found in database");
        }

        $io->success('All tests passed!');
        return Command::SUCCESS;
    }
}
