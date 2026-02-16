<?php
/**
 * Brevo API Test Script
 * 
 * Run this script to test if your Brevo API key works correctly
 * Usage: php test_brevo_api.php YOUR_TEST_EMAIL@example.com
 */

require __DIR__ . '/vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value, '"');
        }
    }
}

$apiKey = $_ENV['BREVO_API_KEY'] ?? '';
$fromEmail = $_ENV['MAIL_FROM_EMAIL'] ?? 'autolearn66@gmail.com';
$fromName = $_ENV['MAIL_FROM_NAME'] ?? 'AutoLearn';

// Get test email from command line argument
$testEmail = $argv[1] ?? null;

if (!$testEmail) {
    echo "Usage: php test_brevo_api.php YOUR_TEST_EMAIL@example.com\n";
    exit(1);
}

if (empty($apiKey)) {
    echo "❌ Error: BREVO_API_KEY not found in .env file\n";
    exit(1);
}

echo "🔍 Testing Brevo API Configuration\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "API Key: " . substr($apiKey, 0, 15) . "...\n";
echo "From Email: $fromEmail\n";
echo "From Name: $fromName\n";
echo "Test Email: $testEmail\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$client = new \GuzzleHttp\Client();

try {
    echo "📤 Sending test email via Brevo API...\n";
    
    $response = $client->post('https://api.brevo.com/v3/smtp/email', [
        'headers' => [
            'api-key' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
        'json' => [
            'sender' => [
                'name' => $fromName,
                'email' => $fromEmail
            ],
            'to' => [
                [
                    'email' => $testEmail,
                    'name' => 'Test User'
                ]
            ],
            'subject' => 'AutoLearn - Brevo API Test',
            'htmlContent' => '<html><body><h1>Success!</h1><p>Your Brevo API is working correctly.</p><p>This is a test email from AutoLearn.</p></body></html>',
            'textContent' => "Success!\n\nYour Brevo API is working correctly.\n\nThis is a test email from AutoLearn."
        ],
        'timeout' => 10
    ]);
    
    $statusCode = $response->getStatusCode();
    $responseBody = json_decode($response->getBody()->getContents(), true);
    
    echo "✅ SUCCESS!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Status Code: $statusCode\n";
    echo "Message ID: " . ($responseBody['messageId'] ?? 'N/A') . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n📧 Check your inbox at: $testEmail\n";
    echo "💡 If you don't see the email, check your spam folder.\n";
    echo "\n✨ Your Brevo API configuration is working correctly!\n";
    
} catch (\GuzzleHttp\Exception\ClientException $e) {
    $statusCode = $e->getResponse()->getStatusCode();
    $errorBody = $e->getResponse()->getBody()->getContents();
    
    echo "❌ ERROR: HTTP $statusCode\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "$errorBody\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    if ($statusCode === 401) {
        echo "🔑 Your API key is invalid or expired.\n";
        echo "   → Get a new key from: https://app.brevo.com/settings/keys/api\n";
    } elseif ($statusCode === 403) {
        echo "🚫 Sender email not verified.\n";
        echo "   → Verify your sender email at: https://app.brevo.com/senders\n";
    } elseif ($statusCode === 400) {
        echo "⚠️  Bad request. Check your email addresses and content.\n";
    }
    
    exit(1);
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n💡 Troubleshooting:\n";
    echo "   1. Check your internet connection\n";
    echo "   2. Verify BREVO_API_KEY in .env file\n";
    echo "   3. Make sure GuzzleHTTP is installed: composer require guzzlehttp/guzzle\n";
    exit(1);
}
