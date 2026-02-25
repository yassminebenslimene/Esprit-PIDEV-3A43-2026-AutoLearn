<?php

require __DIR__.'/vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();

$mailService = $container->get('App\Service\BrevoMailService');

try {
    echo "Testing reactivation email...\n";
    $mailService->sendReactivationEmail(
        'test@example.com',
        'Test User',
        'http://localhost:8000/login'
    );
    echo "✅ Reactivation email sent successfully!\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
