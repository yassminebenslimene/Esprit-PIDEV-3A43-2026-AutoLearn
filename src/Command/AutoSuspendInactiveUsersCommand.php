<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Etudiant;
use App\Repository\UserRepository;
use App\Service\BrevoMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:auto-suspend-inactive-users',
    description: 'Suspend automatiquement les étudiants inactifs depuis 7 jours ou plus',
)]
class AutoSuspendInactiveUsersCommand extends Command
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private BrevoMailService $mailService;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        BrevoMailService $mailService
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->mailService = $mailService;
    }

    protected function configure(): void
    {
        $this
            ->addOption('days', 'd', InputOption::VALUE_OPTIONAL, 'Nombre de jours d\'inactivité avant suspension', 7)
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simuler sans effectuer les suspensions')
            ->setHelp('Cette commande suspend automatiquement les étudiants qui n\'ont pas été actifs depuis X jours (par défaut 7 jours)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = (int) $input->getOption('days');
        $dryRun = $input->getOption('dry-run');

        $io->title('Suspension Automatique des Utilisateurs Inactifs');
        $io->info(sprintf('Recherche des étudiants inactifs depuis %d jours ou plus...', $days));

        if ($dryRun) {
            $io->warning('MODE SIMULATION - Aucune modification ne sera effectuée');
        }

        // Calculate the date threshold
        $thresholdDate = new \DateTime();
        $thresholdDate->modify(sprintf('-%d days', $days));

        // Find inactive students
        $qb = $this->userRepository->createQueryBuilder('u');
        $inactiveUsers = $qb
            ->where('u.role = :role')
            ->andWhere('u.isSuspended = :suspended')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('u.lastLoginAt'),
                $qb->expr()->lt('u.lastLoginAt', ':threshold')
            ))
            ->setParameter('role', 'ETUDIANT')
            ->setParameter('suspended', false)
            ->setParameter('threshold', $thresholdDate)
            ->getQuery()
            ->getResult();

        if (empty($inactiveUsers)) {
            $io->success('Aucun étudiant inactif trouvé.');
            return Command::SUCCESS;
        }

        $io->section(sprintf('%d étudiant(s) inactif(s) trouvé(s)', count($inactiveUsers)));

        $suspendedCount = 0;
        $errorCount = 0;

        foreach ($inactiveUsers as $user) {
            $lastLogin = $user->getLastLoginAt();
            $inactiveDays = $lastLogin 
                ? (new \DateTime())->diff($lastLogin)->days 
                : (new \DateTime())->diff($user->getCreatedAt())->days;

            $io->text(sprintf(
                '- %s %s (%s) - Inactif depuis %d jours',
                $user->getPrenom(),
                $user->getNom(),
                $user->getEmail(),
                $inactiveDays
            ));

            if (!$dryRun) {
                try {
                    // Suspend the user
                    $user->setIsSuspended(true);
                    $user->setSuspendedAt(new \DateTime());
                    $user->setSuspensionReason('Compte inactif - Inactivité prolongée (suspension automatique)');
                    $user->setSuspendedBy(null); // null = système automatique

                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    // Send email to student
                    try {
                        $this->mailService->sendSuspensionEmail(
                            $user->getEmail(),
                            $user->getPrenom() . ' ' . $user->getNom(),
                            sprintf('Votre compte a été automatiquement suspendu après %d jours d\'inactivité', $inactiveDays)
                        );
                    } catch (\Exception $e) {
                        $io->warning(sprintf('Email étudiant non envoyé: %s', $e->getMessage()));
                    }

                    // Send notification to all admins
                    $this->notifyAdmins($user, $inactiveDays);

                    $suspendedCount++;
                    $io->success(sprintf('✓ %s %s suspendu', $user->getPrenom(), $user->getNom()));

                } catch (\Exception $e) {
                    $errorCount++;
                    $io->error(sprintf('✗ Erreur pour %s %s: %s', $user->getPrenom(), $user->getNom(), $e->getMessage()));
                }
            }
        }

        if ($dryRun) {
            $io->note(sprintf('%d étudiant(s) seraient suspendus', count($inactiveUsers)));
        } else {
            $io->success(sprintf(
                'Terminé! %d étudiant(s) suspendu(s), %d erreur(s)',
                $suspendedCount,
                $errorCount
            ));
        }

        return Command::SUCCESS;
    }

    private function notifyAdmins(User $user, int $inactiveDays): void
    {
        // Find all admins
        $admins = $this->userRepository->findBy(['role' => 'ADMIN']);

        foreach ($admins as $admin) {
            try {
                $this->mailService->sendAdminNotificationInactiveSuspension(
                    $admin->getEmail(),
                    $admin->getPrenom() . ' ' . $admin->getNom(),
                    $user->getPrenom() . ' ' . $user->getNom(),
                    $user->getEmail(),
                    $inactiveDays
                );
            } catch (\Exception $e) {
                // Log but don't fail if admin notification fails
                error_log(sprintf('Failed to notify admin %s: %s', $admin->getEmail(), $e->getMessage()));
            }
        }
    }
}
