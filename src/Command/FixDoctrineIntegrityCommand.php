<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'app:fix-doctrine-integrity',
    description: 'Fix Doctrine integrity issues automatically'
)]
class FixDoctrineIntegrityCommand extends Command
{
    private string $entityPath;
    private array $fixes = [];

    public function __construct()
    {
        parent::__construct();
        $this->entityPath = __DIR__ . '/../Entity';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Fixing Doctrine Integrity Issues');

        // Fix 1: Bidirectional Association Inconsistencies
        $this->fixBidirectionalAssociations($io);

        // Fix 2: Property Type Mismatches
        $this->fixPropertyTypeMismatches($io);

        // Fix 3: Generate migration for database changes
        $io->section('Summary');
        $io->success(sprintf('Fixed %d issues', count($this->fixes)));
        
        foreach ($this->fixes as $fix) {
            $io->writeln("✅ $fix");
        }

        $io->note('Run: php bin/console doctrine:schema:update --dump-sql to see database changes');
        $io->note('Then: php bin/console make:migration to create a migration');

        return Command::SUCCESS;
    }

    private function fixBidirectionalAssociations(SymfonyStyle $io): void
    {
        $io->section('Fixing Bidirectional Association Inconsistencies');

        $associations = [
            'Challenge' => [
                'userChallenges' => ['cascade' => ['persist', 'remove'], 'orphanRemoval' => true],
                'votes' => ['cascade' => ['persist', 'remove'], 'orphanRemoval' => true],
            ],
            'Quiz' => [
                'questions' => ['cascade' => ['persist', 'remove'], 'orphanRemoval' => true],
            ],
            'Question' => [
                'options' => ['cascade' => ['persist', 'remove'], 'orphanRemoval' => true],
            ],
            'GestionDeCours\\Cours' => [
                'chapitres' => ['cascade' => ['persist', 'remove'], 'orphanRemoval' => true],
            ],
            'Communaute' => [
                'posts' => ['cascade' => ['persist', 'remove'], 'orphanRemoval' => true],
            ],
            'Post' => [
                'commentaires' => ['cascade' => ['persist', 'remove'], 'orphanRemoval' => true],
            ],
            'Evenement' => [
                'participations' => ['cascade' => ['persist', 'remove'], 'orphanRemoval' => true],
            ],
        ];

        foreach ($associations as $entity => $fields) {
            $this->updateEntityAssociations($entity, $fields, $io);
        }
    }

    private function updateEntityAssociations(string $entityName, array $fields, SymfonyStyle $io): void
    {
        $filePath = $this->entityPath . '/' . str_replace('\\', '/', $entityName) . '.php';
        
        if (!file_exists($filePath)) {
            return;
        }

        $content = file_get_contents($filePath);
        $modified = false;

        foreach ($fields as $fieldName => $config) {
            $cascade = $config['cascade'] ?? [];
            $orphanRemoval = $config['orphanRemoval'] ?? false;

            // Find OneToMany annotation
            $pattern = '/(#\[ORM\\\\OneToMany\([^]]*targetEntity:\s*[^,\]]+)([^]]*)\]/';
            
            if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $index => $match) {
                    $fullMatch = $match[0];
                    $position = $match[1];
                    
                    // Check if this is the right field
                    $nextLines = substr($content, $position, 500);
                    if (!preg_match('/private.*\$' . $fieldName . '/', $nextLines)) {
                        continue;
                    }

                    $newAnnotation = $fullMatch;
                    
                    // Add or update cascade
                    if (!empty($cascade)) {
                        $cascadeStr = '["' . implode('", "', $cascade) . '"]';
                        if (strpos($newAnnotation, 'cascade:') !== false) {
                            $newAnnotation = preg_replace('/cascade:\s*\[[^\]]*\]/', 'cascade: ' . $cascadeStr, $newAnnotation);
                        } else {
                            $newAnnotation = str_replace(']', ', cascade: ' . $cascadeStr . ']', $newAnnotation);
                        }
                    }

                    // Add or update orphanRemoval
                    if ($orphanRemoval) {
                        if (strpos($newAnnotation, 'orphanRemoval:') !== false) {
                            $newAnnotation = preg_replace('/orphanRemoval:\s*(true|false)/', 'orphanRemoval: true', $newAnnotation);
                        } else {
                            $newAnnotation = str_replace(']', ', orphanRemoval: true]', $newAnnotation);
                        }
                    }

                    if ($newAnnotation !== $fullMatch) {
                        $content = substr_replace($content, $newAnnotation, $position, strlen($fullMatch));
                        $modified = true;
                        $this->fixes[] = "$entityName::$fieldName - Updated cascade and orphanRemoval";
                    }
                }
            }
        }

        if ($modified) {
            file_put_contents($filePath, $content);
            $io->writeln("✅ Updated $entityName");
        }
    }

    private function fixPropertyTypeMismatches(SymfonyStyle $io): void
    {
        $io->section('Fixing Property Type Mismatches');

        $typeFixe = [
            'Question' => [
                'texteQuestion' => 'string',
                'point' => 'int',
            ],
            'Quiz' => [
                'titre' => 'string',
                'description' => 'string',
                'etat' => 'string',
            ],
            'Chapitre' => [
                'titre' => 'string',
                'contenu' => 'string',
                'ordre' => 'int',
            ],
            'Challenge' => [
                'titre' => 'string',
                'description' => 'string',
                'duree' => 'int',
                'niveau' => 'string',
            ],
            'Option' => [
                'texteOption' => 'string',
                'estCorrecte' => 'bool',
            ],
            'GestionDeCours\\Cours' => [
                'titre' => 'string',
                'description' => 'string',
                'matiere' => 'string',
            ],
        ];

        foreach ($typeFixe as $entity => $fields) {
            $this->fixEntityPropertyTypes($entity, $fields, $io);
        }
    }

    private function fixEntityPropertyTypes(string $entityName, array $fields, SymfonyStyle $io): void
    {
        $filePath = $this->entityPath . '/' . str_replace('\\', '/', $entityName) . '.php';
        
        if (!file_exists($filePath)) {
            return;
        }

        $content = file_get_contents($filePath);
        $modified = false;

        foreach ($fields as $fieldName => $type) {
            // Remove nullable type hint (change ?string to string, ?int to int, ?bool to bool)
            $pattern = '/(private\s+)\?' . $type . '(\s+\$' . $fieldName . ')/';
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, '$1' . $type . '$2', $content);
                $modified = true;
                $this->fixes[] = "$entityName::$fieldName - Removed nullable type";
            }
        }

        if ($modified) {
            file_put_contents($filePath, $content);
            $io->writeln("✅ Updated $entityName property types");
        }
    }
}
