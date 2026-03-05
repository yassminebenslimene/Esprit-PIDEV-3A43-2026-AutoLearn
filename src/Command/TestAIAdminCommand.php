<?php

namespace App\Command;

use App\Service\GroqService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-ai-admin',
    description: 'Test AI Assistant with real admin context and database data'
)]
class TestAIAdminCommand extends Command
{
    private GroqService $groqService;
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(
        GroqService $groqService,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ) {
        parent::__construct();
        $this->groqService = $groqService;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('🤖 Test AI Admin with Real Database Context');
        
        // Collect real database data
        $allUsers = $this->userRepository->findAll();
        $students = array_filter($allUsers, fn($u) => $u->getRole() === 'ETUDIANT');
        $activeStudents = array_filter($students, fn($u) => !$u->getIsSuspended());
        
        $io->section('📊 Real Database Stats');
        $io->table(
            ['Metric', 'Value'],
            [
                ['Total Users', count($allUsers)],
                ['Total Students', count($students)],
                ['Active Students', count($activeStudents)],
                ['Suspended Students', count($students) - count($activeStudents)],
            ]
        );
        
        // Build context with real data
        $context = [
            'stats' => [
                'total_users' => count($allUsers),
                'total_students' => count($students),
                'active_students' => count($activeStudents),
                'suspended_users' => count($allUsers) - count($activeStudents),
            ],
            'all_users' => array_map(function($u) {
                return [
                    'id' => $u->getId(),
                    'nom' => $u->getNom(),
                    'prenom' => $u->getPrenom(),
                    'email' => $u->getEmail(),
                    'role' => $u->getRole(),
                    'niveau' => method_exists($u, 'getNiveau') ? $u->getNiveau() : null,
                    'suspended' => $u->getIsSuspended(),
                ];
            }, array_slice($allUsers, 0, 20))
        ];
        
        $contextJson = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        // Test 1: Combien d'étudiants actifs?
        $io->section('Test 1: Combien d\'étudiants actifs?');
        
        $systemPrompt = <<<PROMPT
Tu es un assistant IA pour les ADMINISTRATEURS sur AutoLearn.

DONNÉES COMPLÈTES DE LA BASE DE DONNÉES:
{$contextJson}

⚠️ RÈGLES CRITIQUES:
1. Utilise UNIQUEMENT les données ci-dessus
2. N'invente AUCUNE donnée
3. Réponds de façon ultra-concise (1 phrase courte)

Réponds maintenant à la question de l'administrateur.
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => 'Combien d\'étudiants actifs?']
        ];
        
        $response1 = $this->groqService->chat($messages);
        
        if ($response1) {
            $io->success('✅ Groq Response');
            $io->text($response1);
        } else {
            $io->error('❌ No response from Groq');
        }
        
        // Test 2: Filtre les étudiants débutants
        $io->section('Test 2: Filtre les étudiants de niveau débutant');
        
        $messages2 = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => 'Filtre les étudiants de niveau débutant']
        ];
        
        $response2 = $this->groqService->chat($messages2);
        
        if ($response2) {
            $io->success('✅ Groq Response');
            $io->text(substr($response2, 0, 800));
        } else {
            $io->error('❌ No response from Groq');
        }
        
        $io->success('🎉 Tests completed!');
        
        return Command::SUCCESS;
    }
}
