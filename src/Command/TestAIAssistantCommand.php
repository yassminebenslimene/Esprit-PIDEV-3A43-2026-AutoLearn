<?php

namespace App\Command;

use App\Service\AIAssistantService;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-ai-assistant',
    description: 'Test AI Assistant with admin queries'
)]
class TestAIAssistantCommand extends Command
{
    private AIAssistantService $aiAssistant;
    private UserRepository $userRepository;

    public function __construct(
        AIAssistantService $aiAssistant,
        UserRepository $userRepository
    ) {
        parent::__construct();
        $this->aiAssistant = $aiAssistant;
        $this->userRepository = $userRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('🤖 Test AI Assistant Admin');
        
        // Check if we have admin users
        $admins = $this->userRepository->findBy(['role' => 'ADMIN'], null, 1);
        if (empty($admins)) {
            $io->warning('⚠️ No admin users found in database. Testing without authentication.');
        } else {
            $admin = $admins[0];
            $io->info('Testing with admin: ' . $admin->getEmail());
        }
        
        // Check database stats
        $totalStudents = $this->userRepository->count(['role' => 'ETUDIANT']);
        $totalAdmins = $this->userRepository->count(['role' => 'ADMIN']);
        $io->info("Database: {$totalStudents} students, {$totalAdmins} admins");
        
        // Test 1: Compter les étudiants actifs
        $io->section('Test 1: Combien d\'étudiants actifs?');
        $result1 = $this->aiAssistant->ask('Combien d\'étudiants actifs?');
        
        if ($result1['success']) {
            $io->success('✅ Response received');
            $io->text('Response: ' . $result1['response']);
            $io->text('Language: ' . $result1['language']);
            $io->text('Duration: ' . $result1['duration_ms'] . 'ms');
            if (isset($result1['database_access'])) {
                $io->text('Database Access: ' . ($result1['database_access'] ? 'YES' : 'NO'));
            }
        } else {
            $io->error('❌ Failed: ' . ($result1['error'] ?? 'Unknown error'));
        }
        
        // Test 2: Filtrer les étudiants débutants
        $io->section('Test 2: Filtre les étudiants de niveau débutant');
        $result2 = $this->aiAssistant->ask('Filtre les étudiants de niveau débutant');
        
        if ($result2['success']) {
            $io->success('✅ Response received');
            $io->text('Response: ' . substr($result2['response'], 0, 500) . '...');
            $io->text('Language: ' . $result2['language']);
            $io->text('Duration: ' . $result2['duration_ms'] . 'ms');
        } else {
            $io->error('❌ Failed: ' . ($result2['error'] ?? 'Unknown error'));
        }
        
        $io->success('🎉 AI Assistant tests completed!');
        $io->note('For full testing with admin context, use the web interface while logged in as admin.');
        
        return Command::SUCCESS;
    }
}
