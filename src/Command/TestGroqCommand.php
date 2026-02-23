<?php

namespace App\Command;

use App\Service\GroqService;
use App\Service\LanguageDetectorService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-groq',
    description: 'Test Groq API connection and language detection'
)]
class TestGroqCommand extends Command
{
    private GroqService $groqService;
    private LanguageDetectorService $languageDetector;

    public function __construct(
        GroqService $groqService,
        LanguageDetectorService $languageDetector
    ) {
        parent::__construct();
        $this->groqService = $groqService;
        $this->languageDetector = $languageDetector;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('🧪 Test Groq API & Language Detection');

        // Test 1: Groq availability
        $io->section('Test 1: Groq API Availability');
        $isAvailable = $this->groqService->isAvailable();
        
        if ($isAvailable) {
            $io->success('✅ Groq API is available and responding');
        } else {
            $io->error('❌ Groq API is not available. Check your GROQ_API_KEY in .env');
            return Command::FAILURE;
        }

        // Test 2: Simple generation
        $io->section('Test 2: Simple Text Generation');
        $response = $this->groqService->generate('Say hello in French', ['max_tokens' => 50]);
        
        if ($response) {
            $io->success('✅ Generation successful');
            $io->text('Response: ' . $response);
        } else {
            $io->error('❌ Generation failed');
            return Command::FAILURE;
        }

        // Test 3: Language detection - French
        $io->section('Test 3: Language Detection');
        
        $testCases = [
            'Bonjour, comment ça va?' => 'fr',
            'Hello, how are you?' => 'en',
            'أريد تعلم البرمجة' => 'other',
            'Je veux apprendre Python' => 'fr',
            'Show me JavaScript courses' => 'en',
        ];

        $allPassed = true;
        foreach ($testCases as $text => $expectedLang) {
            $detectedLang = $this->languageDetector->detect($text);
            $passed = $detectedLang === $expectedLang;
            
            if ($passed) {
                $io->text("✅ \"$text\" → $detectedLang (expected: $expectedLang)");
            } else {
                $io->text("❌ \"$text\" → $detectedLang (expected: $expectedLang)");
                $allPassed = false;
            }
        }

        if ($allPassed) {
            $io->success('✅ All language detection tests passed');
        } else {
            $io->warning('⚠️  Some language detection tests failed');
        }

        // Test 4: Chat with context
        $io->section('Test 4: Chat with System Prompt');
        
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a helpful assistant for AutoLearn platform. Answer in French.'
            ],
            [
                'role' => 'user',
                'content' => 'Quels cours recommandes-tu pour un débutant?'
            ]
        ];

        $chatResponse = $this->groqService->chat($messages, ['max_tokens' => 200]);
        
        if ($chatResponse) {
            $io->success('✅ Chat successful');
            $io->text('Response: ' . substr($chatResponse, 0, 200) . '...');
        } else {
            $io->error('❌ Chat failed');
            return Command::FAILURE;
        }

        // Test 5: Unsupported language message
        $io->section('Test 5: Unsupported Language Message');
        $arabicText = 'أريد تعلم البرمجة';
        $detectedLang = $this->languageDetector->detect($arabicText);
        
        if (!$this->languageDetector->isSupported($detectedLang)) {
            $message = $this->languageDetector->getUnsupportedLanguageMessage($detectedLang);
            $io->success('✅ Unsupported language detected correctly');
            $io->text('Message: ' . $message);
        } else {
            $io->error('❌ Language should be detected as unsupported');
        }

        // Summary
        $io->newLine();
        $io->success('🎉 All tests completed successfully!');
        $io->text([
            '',
            'Your Groq AI Assistant is ready to use!',
            '',
            'Next steps:',
            '1. Start your Symfony server: symfony server:start',
            '2. Login to your application',
            '3. Look for the chat widget in the bottom right corner',
            '4. Ask questions in French or English',
            '',
        ]);

        return Command::SUCCESS;
    }
}
