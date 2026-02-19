<?php

namespace App\Command;

use App\Service\BrevoMailService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TestBrevoCommand extends Command
{
    protected static $defaultName = 'app:test-brevo';
    
    private $mailService;
    private $router;
    
    public function __construct(BrevoMailService $mailService, UrlGeneratorInterface $router)
    {
        $this->mailService = $mailService;
        $this->router = $router;
        parent::__construct();
    }
    
    protected function configure()
    {
        $this->setDescription('Test Brevo email sending');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Testing Brevo email...');
        
        try {
            $loginUrl = $this->router->generate('backoffice_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
            
            $this->mailService->sendRegistrationConfirmation(
                'autolearn66@gmail.com',
                'Test User',
                $loginUrl
            );
            
            $output->writeln('<info>Email sent successfully!</info>');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}