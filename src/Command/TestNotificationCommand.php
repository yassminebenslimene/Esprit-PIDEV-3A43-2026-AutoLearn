<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Service\NotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-notification',
    description: 'Crée une notification de test pour vérifier le système'
)]
class TestNotificationCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private NotificationService $notificationService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('🔔 Test du système de notifications');

        // Trouver un étudiant pour le test
        $student = $this->userRepository->findOneBy(['role' => 'ETUDIANT']);

        if (!$student) {
            $io->error('Aucun étudiant trouvé dans la base de données');
            return Command::FAILURE;
        }

        $io->info(sprintf('Création d\'une notification de test pour : %s %s (%s)', 
            $student->getPrenom(), 
            $student->getNom(), 
            $student->getEmail()
        ));

        try {
            // Créer une notification de test
            $this->notificationService->sendNotification(
                $student,
                'inactivity_reminder',
                '⏰ Rappel d\'activité',
                'Bonjour ' . $student->getPrenom() . ', nous avons remarqué que vous n\'avez pas validé de chapitre depuis 5 jours. Continuez votre apprentissage pour progresser ! 🚀'
            );

            $io->success('✓ Notification de test créée avec succès !');
            $io->note('Visitez /notifications pour voir la notification');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur lors de la création de la notification : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
