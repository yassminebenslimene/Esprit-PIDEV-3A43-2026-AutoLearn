<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-audit-query',
    description: 'Test the audit query to see what data is returned',
)]
class TestAuditQueryCommand extends Command
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
            // Check which audit tables exist
            $tables = $this->connection->executeQuery(
                "SELECT TABLE_NAME FROM information_schema.TABLES 
                 WHERE TABLE_SCHEMA = DATABASE() 
                 AND TABLE_NAME LIKE '%_audit' 
                 AND TABLE_NAME != 'user_audit'"
            )->fetchAllAssociative();

            $io->section('Found Audit Tables:');
            foreach ($tables as $table) {
                $io->text('- ' . $table['TABLE_NAME']);
            }

            $queries = [];

            // Build UNION query for all existing content audit tables
            foreach ($tables as $table) {
                $tableName = $table['TABLE_NAME'];

                // Determine the display name column based on table
                $nameColumn = 'titre'; // Default for most tables
                if ($tableName === 'communaute_audit' || $tableName === 'equipe_audit') {
                    $nameColumn = 'nom';
                } elseif ($tableName === 'commentaire_audit') {
                    $nameColumn = 'contenu';
                } elseif ($tableName === 'exercice_audit') {
                    $nameColumn = 'question';
                }

                // Extract entity type from table name (remove _audit suffix)
                $entityType = str_replace('_audit', '', $tableName);

                $queries[] = "
                    SELECT '$entityType' as entity_type, r.id, r.timestamp, r.username,
                           t.id as entity_id, t.revtype, CONVERT(t.$nameColumn USING utf8mb4) COLLATE utf8mb4_unicode_ci as nom
                    FROM revisions r
                    INNER JOIN $tableName t ON r.id = t.rev
                    WHERE t.id IS NOT NULL
                ";
            }

            if (!empty($queries)) {
                $contentSql = implode(' UNION ALL ', $queries) . " ORDER BY timestamp DESC LIMIT 100";

                $io->section('Executing Query:');
                $io->text($contentSql);

                $contentRevisions = $this->connection->executeQuery($contentSql)->fetchAllAssociative();

                $io->section('Results:');
                $io->text('Total rows: ' . count($contentRevisions));

                if (!empty($contentRevisions)) {
                    $io->table(
                        ['Entity Type', 'ID', 'Timestamp', 'Action', 'Name'],
                        array_map(function($row) {
                            return [
                                $row['entity_type'],
                                $row['id'],
                                $row['timestamp'],
                                $row['revtype'],
                                substr($row['nom'], 0, 50)
                            ];
                        }, array_slice($contentRevisions, 0, 10))
                    );
                }

                $io->success('Query executed successfully!');
            } else {
                $io->warning('No audit tables found');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error: ' . $e->getMessage());
            $io->note('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
