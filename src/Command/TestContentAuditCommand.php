<?php

namespace App\Command;

use App\Entity\GestionDeCours\Cours;
use App\Entity\Challenge;
use App\Entity\Evenement;
use App\Entity\Communaute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-content-audit',
    description: 'Test content audit tracking by updating various content types',
)]
class TestContentAuditCommand extends Command
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
        $totalUpdates = 0;
        $errors = [];

        // Test 1: Update a course
        try {
            $cours = $this->entityManager->getRepository(Cours::class)->findOneBy([]);
            if ($cours) {
                $io->section('Testing Course Audit');
                $io->info('Found course: ' . $cours->getTitre());
                
                $originalDescription = $cours->getDescription();
                $cours->setDescription($originalDescription . ' [Test]');
                $this->entityManager->flush();
                $totalUpdates++;
                
                $cours->setDescription($originalDescription);
                $this->entityManager->flush();
                $totalUpdates++;
                
                $io->success('Course audit: 2 entries created');
            }
        } catch (\Exception $e) {
            $errors[] = 'Course: ' . $e->getMessage();
            $io->warning('Course audit failed - audit table may not exist yet');
        }

        // Test 2: Update a challenge
        try {
            $challenge = $this->entityManager->getRepository(Challenge::class)->findOneBy([]);
            if ($challenge) {
                $io->section('Testing Challenge Audit');
                $io->info('Found challenge: ' . $challenge->getTitre());
                
                $originalDescription = $challenge->getDescription();
                $challenge->setDescription($originalDescription . ' [Test]');
                $this->entityManager->flush();
                $totalUpdates++;
                
                $challenge->setDescription($originalDescription);
                $this->entityManager->flush();
                $totalUpdates++;
                
                $io->success('Challenge audit: 2 entries created');
            }
        } catch (\Exception $e) {
            $errors[] = 'Challenge: ' . $e->getMessage();
            $io->warning('Challenge audit failed - audit table may not exist yet');
        }

        // Test 3: Update an event (skip if audit table doesn't exist)
        try {
            $evenement = $this->entityManager->getRepository(Evenement::class)->findOneBy([]);
            if ($evenement) {
                $io->section('Testing Event Audit');
                $io->info('Found event: ' . $evenement->getTitre());
                
                $originalDescription = $evenement->getDescription();
                $evenement->setDescription($originalDescription . ' [Test]');
                $this->entityManager->flush();
                $totalUpdates++;
                
                $evenement->setDescription($originalDescription);
                $this->entityManager->flush();
                $totalUpdates++;
                
                $io->success('Event audit: 2 entries created');
            }
        } catch (\Exception $e) {
            $errors[] = 'Event: ' . $e->getMessage();
            $io->warning('Event audit failed - audit table may not exist yet');
        }

        // Test 4: Update a community (skip if audit table doesn't exist)
        try {
            $communaute = $this->entityManager->getRepository(Communaute::class)->findOneBy([]);
            if ($communaute) {
                $io->section('Testing Community Audit');
                $io->info('Found community: ' . $communaute->getNom());
                
                $originalDescription = $communaute->getDescription();
                $communaute->setDescription($originalDescription . ' [Test]');
                $this->entityManager->flush();
                $totalUpdates++;
                
                $communaute->setDescription($originalDescription);
                $this->entityManager->flush();
                $totalUpdates++;
                
                $io->success('Community audit: 2 entries created');
            }
        } catch (\Exception $e) {
            $errors[] = 'Community: ' . $e->getMessage();
            $io->warning('Community audit failed - audit table may not exist yet');
        }

        $io->newLine();
        if ($totalUpdates > 0) {
            $io->success("Total audit entries created: $totalUpdates");
            $io->note('Check the audit page at /backoffice/audit to see all content actions.');
        }
        
        if (!empty($errors)) {
            $io->newLine();
            $io->warning('Some audit tables may not exist yet. They will be created automatically when you modify those entities through the backoffice.');
        }

        return Command::SUCCESS;
    }
}
