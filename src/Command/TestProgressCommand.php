<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\GestionDeCours\Chapitre;
use App\Service\CourseProgressService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-progress',
    description: 'Marque un chapitre comme complété pour tester la progression',
)]
class TestProgressCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CourseProgressService $progressService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('userId', InputArgument::REQUIRED, 'ID de l\'utilisateur')
            ->addArgument('chapitreId', InputArgument::REQUIRED, 'ID du chapitre')
            ->addArgument('score', InputArgument::OPTIONAL, 'Score du quiz (défaut: 80)', 80)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userId = $input->getArgument('userId');
        $chapitreId = $input->getArgument('chapitreId');
        $score = $input->getArgument('score');

        // Récupérer l'utilisateur
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            $io->error("Utilisateur avec l'ID {$userId} introuvable.");
            return Command::FAILURE;
        }

        // Récupérer le chapitre
        $chapitre = $this->entityManager->getRepository(Chapitre::class)->find($chapitreId);
        if (!$chapitre) {
            $io->error("Chapitre avec l'ID {$chapitreId} introuvable.");
            return Command::FAILURE;
        }

        // Marquer le chapitre comme complété
        try {
            $progress = $this->progressService->markChapterAsCompleted($user, $chapitre, $score);
            
            $io->success([
                "Chapitre marqué comme complété !",
                "Utilisateur: {$user->getPrenom()} {$user->getNom()}",
                "Chapitre: {$chapitre->getTitre()}",
                "Score: {$score}%",
                "Date: " . $progress->getCompletedAt()->format('Y-m-d H:i:s')
            ]);

            // Afficher la progression du cours
            $cours = $chapitre->getCours();
            $stats = $this->progressService->getCourseProgressStats($user, $cours);
            
            $io->section("Progression du cours: {$cours->getTitre()}");
            $io->table(
                ['Métrique', 'Valeur'],
                [
                    ['Chapitres complétés', $stats['completed_chapters']],
                    ['Chapitres restants', $stats['remaining_chapters']],
                    ['Total chapitres', $stats['total_chapters']],
                    ['Pourcentage', $stats['percentage'] . '%'],
                    ['Cours terminé', $stats['is_completed'] ? 'Oui 🎉' : 'Non'],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error("Erreur: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
