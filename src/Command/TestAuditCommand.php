<?php

namespace App\Command;

use App\Entity\Etudiant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-audit',
    description: 'Test if audit tracking is working by updating a student',
)]
class TestAuditCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            // Find first student
            $student = $this->em->getRepository(Etudiant::class)->findOneBy([]);
            
            if (!$student) {
                $io->error('No students found in database');
                return Command::FAILURE;
            }

            $io->section('Testing Audit Tracking');
            $io->info('Student found: ' . $student->getPrenom() . ' ' . $student->getNom());
            
            // Make a small change
            $oldNiveau = $student->getNiveau();
            $newNiveau = $oldNiveau === 'DEBUTANT' ? 'INTERMEDIAIRE' : 'DEBUTANT';
            
            $io->info('Changing niveau from ' . $oldNiveau . ' to ' . $newNiveau);
            $student->setNiveau($newNiveau);
            
            // Flush to trigger audit
            $this->em->flush();
            
            $io->success('Student updated! Check the audit trail at /backoffice/audit/');
            
            // Check if audit was recorded
            $connection = $this->em->getConnection();
            $revisionCount = $connection->executeQuery('SELECT COUNT(*) FROM revisions')->fetchOne();
            $userAuditCount = $connection->executeQuery('SELECT COUNT(*) FROM user_audit')->fetchOne();
            
            // Check for etudiant_audit and admin_audit tables
            $tables = $connection->executeQuery("SHOW TABLES LIKE '%audit'")->fetchAllAssociative();
            
            $io->section('Audit Tables Found:');
            foreach ($tables as $table) {
                $tableName = array_values($table)[0];
                $count = $connection->executeQuery(
                    'SELECT COUNT(*) FROM ' . $connection->quoteIdentifier($tableName)
                )->fetchOne();
                $io->writeln("  - $tableName: $count entries");
            }
            
            $io->table(
                ['Table', 'Count'],
                [
                    ['revisions', $revisionCount],
                    ['user_audit', $userAuditCount],
                ]
            );
            
            if ($userAuditCount > 0) {
                $io->success('✅ Audit tracking is WORKING!');
                
                // Show last audit entry
                $lastAudit = $connection->executeQuery(
                    'SELECT * FROM user_audit ORDER BY rev DESC LIMIT 1'
                )->fetchAssociative();
                
                $io->section('Last Audit Entry:');
                $io->table(
                    ['Field', 'Value'],
                    [
                        ['rev', $lastAudit['rev']],
                        ['revtype', $lastAudit['revtype']],
                        ['userId', $lastAudit['userId']],
                        ['nom', $lastAudit['nom']],
                        ['prenom', $lastAudit['prenom']],
                        ['discr', $lastAudit['discr']],
                    ]
                );
            } else {
                $io->error('❌ Audit tracking is NOT working - no entries in user_audit table');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Test failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
