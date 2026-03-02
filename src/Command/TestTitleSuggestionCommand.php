<?php

namespace App\Command;

use App\Service\TitleSuggestionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-title',
    description: 'Test title suggestion based on content',
)]
class TestTitleSuggestionCommand extends Command
{
    private TitleSuggestionService $titleService;

    public function __construct(TitleSuggestionService $titleService)
    {
        parent::__construct();
        $this->titleService = $titleService;
    }

    protected function configure(): void
    {
        $this->addArgument('content', InputArgument::OPTIONAL, 'Content to generate title from');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $content = $input->getArgument('content');
        
        if (!$content) {
            // Exemples de test
            $examples = [
                'Dans ce tutoriel, nous allons apprendre les bases de la programmation Python. Nous couvrirons les variables, les boucles, les fonctions et bien plus encore.',
                'Rejoignez notre communauté de développeurs passionnés ! Partagez vos projets, posez des questions et apprenez ensemble.',
                'Découvrez les meilleures pratiques pour créer des applications web modernes avec React et Node.js.',
            ];
            
            $io->title('Test de suggestion de titres');
            
            foreach ($examples as $index => $example) {
                $io->section('Exemple ' . ($index + 1));
                $io->text('Contenu: ' . substr($example, 0, 100) . '...');
                
                $result = $this->titleService->suggestTitle($example);
                
                $io->success('Titre suggéré: ' . $result['title']);
                
                if (!empty($result['alternatives'])) {
                    $io->text('Alternatives:');
                    foreach ($result['alternatives'] as $alt) {
                        $io->text('  • ' . $alt);
                    }
                }
                $io->newLine();
            }
        } else {
            $io->title('Suggestion de titre');
            $io->text('Contenu: ' . $content);
            
            $result = $this->titleService->suggestTitle($content);
            
            $io->success('Titre suggéré: ' . $result['title']);
            
            if (!empty($result['alternatives'])) {
                $io->text('Alternatives:');
                foreach ($result['alternatives'] as $alt) {
                    $io->text('  • ' . $alt);
                }
            }
        }

        $io->success('Test terminé !');

        return Command::SUCCESS;
    }
}
