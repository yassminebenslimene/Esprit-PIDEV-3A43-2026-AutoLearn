<?php

namespace App\Command;

use App\Service\AiReactionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-reaction',
    description: 'Test du service de suggestion de réactions AI',
)]
class TestReactionCommand extends Command
{
    private AiReactionService $aiReactionService;

    public function __construct(AiReactionService $aiReactionService)
    {
        parent::__construct();
        $this->aiReactionService = $aiReactionService;
    }

    protected function configure(): void
    {
        $this->addArgument('content', InputArgument::OPTIONAL, 'Contenu à analyser');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Test de suggestion de réactions AI');

        $content = $input->getArgument('content');

        if ($content) {
            // Test avec le contenu fourni
            $this->testReaction($io, $content);
        } else {
            // Tests avec des exemples
            $examples = [
                'Ce tutoriel Python est vraiment excellent ! Il explique très bien les concepts de base et les exemples sont clairs. Parfait pour les débutants.',
                'Découvrez cette nouvelle bibliothèque JavaScript qui révolutionne le développement web. Les performances sont incroyables !',
                'Voici une blague de programmeur : Pourquoi les développeurs préfèrent-ils le mode sombre ? Parce que la lumière attire les bugs ! 😄',
                'Cette étude scientifique sur l\'intelligence artificielle présente des résultats fascinants sur l\'apprentissage profond.',
            ];

            foreach ($examples as $index => $example) {
                $io->section('Exemple ' . ($index + 1));
                $io->text('Contenu: ' . substr($example, 0, 80) . '...');
                $io->newLine();
                $this->testReaction($io, $example);
                $io->newLine();
            }
        }

        $io->success('Test terminé !');
        return Command::SUCCESS;
    }

    private function testReaction(SymfonyStyle $io, string $content): void
    {
        $result = $this->aiReactionService->suggestReactions($content);

        $primaryDisplay = $this->aiReactionService->getReactionDisplay($result['primary']);
        
        $io->table(
            ['Propriété', 'Valeur'],
            [
                ['Réaction principale', $primaryDisplay['emoji'] . ' ' . $primaryDisplay['label']],
                ['Réactions secondaires', implode(', ', array_map(function($r) {
                    $display = $this->aiReactionService->getReactionDisplay($r);
                    return $display['emoji'] . ' ' . $display['label'];
                }, $result['secondary']))],
                ['Raison', $result['reason']],
            ]
        );
    }
}
