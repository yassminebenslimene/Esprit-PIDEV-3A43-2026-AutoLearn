<?php

namespace App\Command;

use App\Service\SentimentAnalysisService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-sentiment',
    description: 'Test sentiment analysis on a comment',
)]
class TestSentimentCommand extends Command
{
    private SentimentAnalysisService $sentimentService;

    public function __construct(SentimentAnalysisService $sentimentService)
    {
        parent::__construct();
        $this->sentimentService = $sentimentService;
    }

    protected function configure(): void
    {
        $this->addArgument('text', InputArgument::OPTIONAL, 'Text to analyze');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $text = $input->getArgument('text');
        
        if (!$text) {
            // Exemples de test
            $examples = [
                'Ce cours est vraiment excellent ! J\'ai beaucoup appris.',
                'C\'est nul, je n\'ai rien compris.',
                'Le contenu est correct, rien de spécial.',
            ];
            
            $io->title('Test d\'analyse de sentiment');
            
            foreach ($examples as $example) {
                $io->section('Texte: ' . $example);
                $result = $this->sentimentService->analyzeSentiment($example);
                
                $io->table(
                    ['Propriété', 'Valeur'],
                    [
                        ['Sentiment', $result['sentiment'] . ' ' . $this->sentimentService->getSentimentIcon($result['sentiment'])],
                        ['Score', $result['score']],
                        ['Explication', $result['explanation']],
                    ]
                );
            }
        } else {
            $io->title('Analyse de sentiment');
            $io->text('Texte: ' . $text);
            
            $result = $this->sentimentService->analyzeSentiment($text);
            
            $io->table(
                ['Propriété', 'Valeur'],
                [
                    ['Sentiment', $result['sentiment'] . ' ' . $this->sentimentService->getSentimentIcon($result['sentiment'])],
                    ['Score', $result['score']],
                    ['Explication', $result['explanation']],
                ]
            );
        }

        $io->success('Analyse terminée !');

        return Command::SUCCESS;
    }
}
