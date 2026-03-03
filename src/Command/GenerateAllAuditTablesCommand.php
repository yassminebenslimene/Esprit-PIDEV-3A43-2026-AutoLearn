<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-all-audit-tables',
    description: 'Generate all audit tables by reading entity metadata',
)]
class GenerateAllAuditTablesCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private Connection $connection;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->connection = $entityManager->getConnection();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Generating All Audit Tables');

        // List of entities to create audit tables for
        $entities = [
            'App\Entity\Challenge',
            'App\Entity\Evenement',
            'App\Entity\Communaute',
            'App\Entity\GestionDeCours\Chapitre',
            'App\Entity\Exercice',
            'App\Entity\Quiz',
            'App\Entity\Post',
            'App\Entity\Commentaire',
            'App\Entity\Equipe',
            'App\Entity\GestionDeCours\Ressource',
        ];

        foreach ($entities as $entityClass) {
            try {
                $metadata = $this->entityManager->getClassMetadata($entityClass);
                $tableName = $metadata->getTableName();
                $auditTableName = $tableName . '_audit';

                $io->section("Creating audit table for: $entityClass");
                $io->text("Table: $tableName → $auditTableName");

                // Check if audit table already exists
                $exists = $this->connection->executeQuery(
                    "SELECT COUNT(*) as count FROM information_schema.TABLES 
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?",
                    [$auditTableName]
                )->fetchOne();

                if ($exists > 0) {
                    $io->warning("✓ $auditTableName already exists (skipping)");
                    continue;
                }

                // Build column definitions
                $columns = [];
                $columns[] = "id INT NOT NULL";
                $columns[] = "rev INT NOT NULL";
                $columns[] = "revtype VARCHAR(4) NOT NULL";

                // Add entity columns
                foreach ($metadata->getFieldNames() as $fieldName) {
                    if (in_array($fieldName, ['id'])) {
                        continue; // Skip id, already added
                    }

                    $fieldMapping = $metadata->getFieldMapping($fieldName);
                    $columnName = $fieldMapping['columnName'] ?? $fieldName;
                    $type = $fieldMapping['type'];

                    // Map Doctrine types to MySQL types
                    $sqlType = match($type) {
                        'string' => 'VARCHAR(255)',
                        'text' => 'TEXT',
                        'integer' => 'INT',
                        'smallint' => 'SMALLINT',
                        'bigint' => 'BIGINT',
                        'boolean' => 'TINYINT(1)',
                        'decimal' => 'DECIMAL(10,2)',
                        'float' => 'FLOAT',
                        'datetime' => 'DATETIME',
                        'date' => 'DATE',
                        'time' => 'TIME',
                        'json' => 'JSON',
                        default => 'VARCHAR(255)',
                    };

                    $columns[] = "$columnName $sqlType DEFAULT NULL";
                }

                // Add foreign key columns (ManyToOne relationships)
                foreach ($metadata->getAssociationMappings() as $assocMapping) {
                    if ($assocMapping['type'] === \Doctrine\ORM\Mapping\ClassMetadata::MANY_TO_ONE) {
                        foreach ($assocMapping['joinColumns'] as $joinColumn) {
                            $columnName = $joinColumn['name'];
                            if (!in_array("$columnName INT DEFAULT NULL", $columns)) {
                                $columns[] = "$columnName INT DEFAULT NULL";
                            }
                        }
                    }
                }

                $columnsSql = implode(",\n                    ", $columns);

                // Create the audit table
                $sql = "
                    CREATE TABLE $auditTableName (
                        $columnsSql,
                        PRIMARY KEY (id, rev),
                        INDEX rev_idx (rev),
                        CONSTRAINT FK_{$auditTableName}_rev FOREIGN KEY (rev) REFERENCES revisions (id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ";

                $this->connection->executeStatement($sql);
                $io->success("✓ Created $auditTableName");

            } catch (\Exception $e) {
                $io->error("Failed to create audit table for $entityClass: " . $e->getMessage());
            }
        }

        $io->newLine();
        $io->success('All audit tables generated!');
        $io->note('Now all admin actions on content will be tracked automatically.');

        return Command::SUCCESS;
    }
}
