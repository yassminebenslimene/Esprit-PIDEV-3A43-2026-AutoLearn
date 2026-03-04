<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:setup-all-audit-tables',
    description: 'Create all audit tables for content entities',
)]
class SetupAllAuditTablesCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $connection = $this->entityManager->getConnection();

        try {
            $io->section('Creating Audit Tables');

            // Challenge audit table
            $io->text('Creating challenge_audit table...');
            $connection->executeStatement("
                CREATE TABLE IF NOT EXISTS challenge_audit (
                    id INT NOT NULL,
                    rev INT NOT NULL,
                    revtype VARCHAR(4) NOT NULL,
                    titre VARCHAR(255) DEFAULT NULL,
                    description VARCHAR(255) DEFAULT NULL,
                    duree INT DEFAULT NULL,
                    niveau VARCHAR(50) DEFAULT NULL,
                    created_by INT DEFAULT NULL,
                    PRIMARY KEY (id, rev),
                    INDEX rev_idx (rev),
                    CONSTRAINT FK_challenge_audit_rev FOREIGN KEY (rev) REFERENCES revisions (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            $io->success('✓ challenge_audit created');

            // Evenement audit table
            $io->text('Creating evenement_audit table...');
            $connection->executeStatement("
                CREATE TABLE IF NOT EXISTS evenement_audit (
                    id INT NOT NULL,
                    rev INT NOT NULL,
                    revtype VARCHAR(4) NOT NULL,
                    titre VARCHAR(255) DEFAULT NULL,
                    description LONGTEXT DEFAULT NULL,
                    date_debut DATETIME DEFAULT NULL,
                    date_fin DATETIME DEFAULT NULL,
                    PRIMARY KEY (id, rev),
                    INDEX rev_idx (rev),
                    CONSTRAINT FK_evenement_audit_rev FOREIGN KEY (rev) REFERENCES revisions (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            $io->success('✓ evenement_audit created');

            // Communaute audit table
            $io->text('Creating communaute_audit table...');
            $connection->executeStatement("
                CREATE TABLE IF NOT EXISTS communaute_audit (
                    id INT NOT NULL,
                    rev INT NOT NULL,
                    revtype VARCHAR(4) NOT NULL,
                    nom VARCHAR(255) DEFAULT NULL,
                    description LONGTEXT DEFAULT NULL,
                    PRIMARY KEY (id, rev),
                    INDEX rev_idx (rev),
                    CONSTRAINT FK_communaute_audit_rev FOREIGN KEY (rev) REFERENCES revisions (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            $io->success('✓ communaute_audit created');

            // Chapitre audit table
            $io->text('Creating chapitre_audit table...');
            $connection->executeStatement("
                CREATE TABLE IF NOT EXISTS chapitre_audit (
                    id INT NOT NULL,
                    rev INT NOT NULL,
                    revtype VARCHAR(4) NOT NULL,
                    titre VARCHAR(255) DEFAULT NULL,
                    contenu LONGTEXT DEFAULT NULL,
                    PRIMARY KEY (id, rev),
                    INDEX rev_idx (rev),
                    CONSTRAINT FK_chapitre_audit_rev FOREIGN KEY (rev) REFERENCES revisions (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            $io->success('✓ chapitre_audit created');

            $io->newLine();
            $io->success('All audit tables created successfully!');
            $io->note('You can now run: php bin/console app:test-content-audit');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
