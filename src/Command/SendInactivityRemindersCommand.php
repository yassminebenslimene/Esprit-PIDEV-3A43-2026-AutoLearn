<?php

namespace App\Command;

use App\Service\InactivityDetectionService;
use App\Service\NotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Commande planifiée pour envoyer des rappels aux étudiants inactifs
 * 
 * Usage :
 * php bin/console app:send-inactivity-reminders
 * php bin/console app:send-inactivity-reminders --dry-run (simulation)
 * 
 * Planification (cron) :
 * 0 9 * * * cd /path/to/project && php bin/console app:send-inactivity-reminders
 * (Tous les jours à 9h00)
 */
#[AsCommand(
    name: 'app:send-inactivity-reminders',
    description: 'Envoie des rappels automatiques aux étudiants inactifs depuis 3 jours'
)]
class SendInactivityRemindersCommand extends Command
{
    public function __construct(
        private InactivityDetectionService $inactivityService,
        private NotificationService $notificationService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Simulation sans envoi réel de notifications'
            )
            ->setHelp(
                'Cette commande détecte les étudiants inactifs depuis 3 jours et leur envoie ' .
                'une notification interne + un SMS de rappel.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');

        $io->title('🔔 Envoi de rappels d\'inactivité');

        if ($dryRun) {
            $io->warning('MODE SIMULATION - Aucune notification ne sera envoyée');
        }

        // 1️⃣ Détection des étudiants inactifs (logique métier)
        $io->section('📊 Détection des étudiants inactifs');
        $inactiveStudents = $this->inactivityService->detectInactiveStudents();

        if (empty($inactiveStudents)) {
            $io->success('✓ Aucun étudiant inactif détecté');
            return Command::SUCCESS;
        }

        $io->info(sprintf('Trouvé %d étudiant(s) inactif(s)', count($inactiveStudents)));

        // 2️⃣ Envoi des notifications (service de notification)
        $io->section('📤 Envoi des notifications');
        $io->progressStart(count($inactiveStudents));

        $stats = [
            'total' => count($inactiveStudents),
            'internal_success' => 0,
            'sms_success' => 0,
            'errors' => 0
        ];

        foreach ($inactiveStudents as $student) {
            $inactivityDays = $this->inactivityService->getInactivityDays($student);

            if (!$dryRun) {
                try {
                    // Envoi double canal : notification interne + SMS
                    $results = $this->notificationService->sendInactivityReminder(
                        $student,
                        $inactivityDays
                    );

                    if ($results['internal']) {
                        $stats['internal_success']++;
                    }
                    if ($results['sms']) {
                        $stats['sms_success']++;
                    }
                } catch (\Exception $e) {
                    $stats['errors']++;
                    $io->error(sprintf(
                        'Erreur pour %s : %s',
                        $student->getEmail(),
                        $e->getMessage()
                    ));
                }
            } else {
                // Mode simulation
                $io->writeln(sprintf(
                    '  [SIMULATION] %s %s (%s) - Inactif depuis %d jours',
                    $student->getPrenom(),
                    $student->getNom(),
                    $student->getEmail(),
                    $inactivityDays
                ));
            }

            $io->progressAdvance();
        }

        $io->progressFinish();

        // 3️⃣ Affichage des statistiques
        $io->section('📈 Résultats');

        if (!$dryRun) {
            $io->table(
                ['Métrique', 'Valeur'],
                [
                    ['Étudiants inactifs détectés', $stats['total']],
                    ['Notifications internes envoyées', $stats['internal_success']],
                    ['SMS envoyés', $stats['sms_success']],
                    ['Erreurs', $stats['errors']]
                ]
            );

            if ($stats['errors'] > 0) {
                $io->warning(sprintf('%d erreur(s) détectée(s)', $stats['errors']));
            } else {
                $io->success('✓ Tous les rappels ont été envoyés avec succès');
            }
        } else {
            $io->note(sprintf(
                'En mode réel, %d notification(s) interne(s) et %d SMS seraient envoyés',
                $stats['total'],
                $stats['total']
            ));
        }

        return Command::SUCCESS;
    }
}
