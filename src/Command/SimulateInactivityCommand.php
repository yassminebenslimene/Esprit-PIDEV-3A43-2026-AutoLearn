<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:simulate-inactivity',
    description: 'Simule l\'inactivité d\'étudiants pour tester la suspension automatique',
)]
class SimulateInactivityCommand extends Command
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addOption('days', 'd', InputOption::VALUE_OPTIONAL, 'Nombre de jours d\'inactivité à simuler', 10)
            ->addOption('count', 'c', InputOption::VALUE_OPTIONAL, 'Nombre d\'étudiants à modifier', 1)
            ->setHelp('Cette commande modifie la date de dernière connexion d\'étudiants pour simuler une inactivité');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = (int) $input->getOption('days');
        $count = (int) $input->getOption('count');

        $io->title('Simulation d\'Inactivité pour Tests');
        $io->info(sprintf('Simulation de %d jours d\'inactivité pour %d étudiant(s)...', $days, $count));

        // Find active students (not suspended)
        $students = $this->userRepository->createQueryBuilder('u')
            ->where('u.role = :role')
            ->andWhere('u.isSuspended = :suspended')
            ->setParameter('role', 'ETUDIANT')
            ->setParameter('suspended', false)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();

        if (empty($students)) {
            $io->error('Aucun étudiant actif trouvé.');
            return Command::FAILURE;
        }

        $io->section(sprintf('%d étudiant(s) trouvé(s)', count($students)));

        $modifiedCount = 0;
        $inactiveDate = new \DateTime();
        $inactiveDate->modify(sprintf('-%d days', $days));

        foreach ($students as $student) {
            $io->text(sprintf(
                '- %s %s (%s)',
                $student->getPrenom(),
                $student->getNom(),
                $student->getEmail()
            ));

            $student->setLastLoginAt($inactiveDate);
            $modifiedCount++;
        }

        $this->entityManager->flush();

        $io->success(sprintf(
            '%d étudiant(s) modifié(s) - Dernière connexion: %s (%d jours)',
            $modifiedCount,
            $inactiveDate->format('d/m/Y H:i'),
            $days
        ));

        $io->note([
            'Prochaines étapes:',
            '1. Exécutez: php bin/console app:auto-suspend-inactive-users --dry-run',
            '2. Vérifiez que les étudiants apparaissent comme inactifs',
            '3. Exécutez: php bin/console app:auto-suspend-inactive-users',
            '4. Vérifiez les emails reçus'
        ]);

        return Command::SUCCESS;
    }
}
