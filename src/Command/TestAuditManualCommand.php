<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Etudiant;

#[AsCommand(
    name: 'app:test-audit-manual',
    description: 'Test audit tracking by modifying a student'
)]
class TestAuditManualCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Testing Audit Bundle...');
        
        // Find first student
        $student = $this->entityManager->getRepository(Etudiant::class)->findOneBy([]);
        
        if (!$student) {
            $output->writeln('<error>No student found!</error>');
            return Command::FAILURE;
        }
        
        $output->writeln('Found student: ' . $student->getPrenom() . ' ' . $student->getNom());
        $oldNom = $student->getNom();
        
        // Modify student
        $student->setNom($oldNom . ' (TEST)');
        $this->entityManager->persist($student);
        $this->entityManager->flush();
        
        $output->writeln('<info>Student modified!</info>');
        
        // Check if revision was created
        $connection = $this->entityManager->getConnection();
        $revisionCount = $connection->executeQuery('SELECT COUNT(*) FROM revisions')->fetchOne();
        $auditCount = $connection->executeQuery('SELECT COUNT(*) FROM etudiant_audit')->fetchOne();
        
        $output->writeln('Total revisions: ' . $revisionCount);
        $output->writeln('Total etudiant_audit records: ' . $auditCount);
        
        if ($revisionCount > 0) {
            $output->writeln('<info>✓ Audit is working!</info>');
            
            // Show last revision
            $lastRevision = $connection->executeQuery(
                'SELECT * FROM revisions ORDER BY id DESC LIMIT 1'
            )->fetchAssociative();
            
            $output->writeln('Last revision:');
            $output->writeln('  ID: ' . $lastRevision['id']);
            $output->writeln('  Timestamp: ' . $lastRevision['timestamp']);
            $output->writeln('  Username: ' . ($lastRevision['username'] ?? 'NULL'));
        } else {
            $output->writeln('<error>✗ Audit is NOT working!</error>');
            $output->writeln('The bundle is not tracking changes.');
        }
        
        // Restore original name
        $student->setNom($oldNom);
        $this->entityManager->persist($student);
        $this->entityManager->flush();
        
        return Command::SUCCESS;
    }
}
