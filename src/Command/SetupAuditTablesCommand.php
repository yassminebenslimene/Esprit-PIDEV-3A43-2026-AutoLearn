<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:setup-audit-tables',
    description: 'Create audit tables for the audit bundle',
)]
class SetupAuditTablesCommand extends Command
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            // Create revisions table
            $io->section('Creating revisions table...');
            $this->connection->executeStatement("
                CREATE TABLE IF NOT EXISTS revisions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    timestamp DATETIME NOT NULL,
                    username VARCHAR(255) DEFAULT NULL
                )
            ");
            $io->success('Revisions table created');

            // Create user_audit table
            $io->section('Creating user_audit table...');
            $this->connection->executeStatement("
                CREATE TABLE IF NOT EXISTS user_audit (
                    id INT,
                    rev INT NOT NULL,
                    revtype VARCHAR(4) NOT NULL,
                    userId INT,
                    nom VARCHAR(50),
                    prenom VARCHAR(50),
                    email VARCHAR(255),
                    password VARCHAR(255),
                    role VARCHAR(20),
                    createdAt DATETIME,
                    isSuspended TINYINT(1),
                    suspendedAt DATETIME,
                    suspensionReason VARCHAR(500),
                    suspendedBy INT,
                    lastLoginAt DATETIME,
                    lastActivityAt DATETIME,
                    discr VARCHAR(255),
                    niveau VARCHAR(50),
                    PRIMARY KEY (id, rev),
                    FOREIGN KEY (rev) REFERENCES revisions(id)
                )
            ");
            $io->success('User_audit table created');

            // Create cours_audit table
            $io->section('Creating cours_audit table...');
            $this->connection->executeStatement("
                CREATE TABLE IF NOT EXISTS cours_audit (
                    id INT,
                    rev INT NOT NULL,
                    revtype VARCHAR(4) NOT NULL,
                    titre VARCHAR(255),
                    description TEXT,
                    matiere VARCHAR(255),
                    niveau VARCHAR(255),
                    duree INT,
                    created_at DATETIME,
                    communaute_id INT,
                    PRIMARY KEY (id, rev),
                    FOREIGN KEY (rev) REFERENCES revisions(id)
                )
            ");
            $io->success('Cours_audit table created');

            $io->success('All audit tables created successfully!');
            $io->note('Now try updating a student or creating a course to test the audit tracking.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Failed to create audit tables: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
