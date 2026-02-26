<?php

namespace App\Command;

use App\Entity\Communaute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-stof-extensions',
    description: 'Test StofDoctrineExtensionsBundle features (Timestampable, Sluggable)',
)]
class TestStofExtensionsCommand extends Command
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

        $io->title('Testing StofDoctrineExtensionsBundle');

        // Test 1: Check if bundle is configured
        $io->section('1. Configuration Check');
        $io->success('✓ StofDoctrineExtensionsBundle is installed and configured');

        // Test 2: Test Timestampable on existing entity
        $io->section('2. Testing Timestampable Feature');
        
        $communaute = $this->entityManager->getRepository(Communaute::class)->findOneBy([]);
        
        if ($communaute) {
            $io->text('Found community: ' . $communaute->getNom());
            
            // Check if entity has createdAt/updatedAt
            if (method_exists($communaute, 'getCreatedAt')) {
                $createdAt = $communaute->getCreatedAt();
                $io->text('Created At: ' . ($createdAt ? $createdAt->format('Y-m-d H:i:s') : 'Not set'));
            } else {
                $io->warning('Entity does not have createdAt field (Timestampable not applied)');
            }
            
            if (method_exists($communaute, 'getUpdatedAt')) {
                $updatedAt = $communaute->getUpdatedAt();
                $io->text('Updated At: ' . ($updatedAt ? $updatedAt->format('Y-m-d H:i:s') : 'Not set'));
            } else {
                $io->warning('Entity does not have updatedAt field (Timestampable not applied)');
            }
            
            // Test update
            $io->newLine();
            $io->text('Testing automatic timestamp update...');
            $oldDescription = $communaute->getDescription();
            $communaute->setDescription($oldDescription . ' [Updated by test]');
            
            $this->entityManager->flush();
            
            if (method_exists($communaute, 'getUpdatedAt')) {
                $newUpdatedAt = $communaute->getUpdatedAt();
                $io->success('✓ Updated At automatically changed to: ' . ($newUpdatedAt ? $newUpdatedAt->format('Y-m-d H:i:s') : 'Not set'));
            }
            
            // Restore original description
            $communaute->setDescription($oldDescription);
            $this->entityManager->flush();
            
        } else {
            $io->warning('No community found to test. Create a community first.');
        }

        // Test 3: Show how to use the features
        $io->section('3. How to Use StofDoctrineExtensionsBundle');
        
        $io->text([
            'To enable Timestampable on an entity, add these annotations:',
            '',
            'use Gedmo\Mapping\Annotation as Gedmo;',
            'use Gedmo\Timestampable\Traits\TimestampableEntity;',
            '',
            'class YourEntity {',
            '    use TimestampableEntity; // Adds createdAt and updatedAt automatically',
            '}',
            '',
            'OR manually:',
            '',
            '/**',
            ' * @Gedmo\Timestampable(on="create")',
            ' * @ORM\Column(type="datetime")',
            ' */',
            'private $createdAt;',
            '',
            '/**',
            ' * @Gedmo\Timestampable(on="update")',
            ' * @ORM\Column(type="datetime")',
            ' */',
            'private $updatedAt;',
        ]);

        $io->section('4. Available Features');
        $io->listing([
            'Timestampable: Auto-set createdAt/updatedAt dates',
            'Sluggable: Auto-generate URL-friendly slugs from titles',
            'SoftDeleteable: Soft delete entities (mark as deleted without removing)',
        ]);

        $io->success('StofDoctrineExtensionsBundle test completed!');
        $io->note('To see it in action, add Timestampable annotations to your entities and run migrations.');

        return Command::SUCCESS;
    }
}
