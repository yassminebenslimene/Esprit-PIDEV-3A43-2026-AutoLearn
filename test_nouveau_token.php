<?php
/**
 * Script de test pour vérifier le nouveau token Hugging Face
 * À exécuter après avoir créé un nouveau token avec la permission "Make calls to Inference Providers"
 */

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/.env');

$apiKey = $_ENV['HUGGINGFACE_API_KEY'] ?? '';
$model = $_ENV['HUGGINGFACE_MODEL'] ?? '';

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  TEST DU NOUVEAU TOKEN HUGGING FACE                        ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Vérification 1: Token configuré
echo "1️⃣  Vérification du token...\n";
if (empty($apiKey)) {
    echo "   ❌ ERREUR: HUGGINGFACE_API_KEY non défini dans .env.local\n";
    exit(1);
}
if (!str_starts_with($apiKey, 'hf_')) {
    echo "   ⚠️  ATTENTION: Le token ne commence pas par 'hf_'\n";
}
echo "   ✅ Token trouvé: " . substr($apiKey, 0, 10) . "... (longueur: " . strlen($apiKey) . ")\n\n";

// Vérification 2: Modèle configuré
echo "2️⃣  Vérification du modèle...\n";
if (empty($model)) {
    echo "   ❌ ERREUR: HUGGINGFACE_MODEL non défini dans .env.local\n";
    exit(1);
}
echo "   ✅ Modèle: $model\n\n";

// Test 3: Appel API
echo "3️⃣  Test de l'API Inference Providers...\n";
echo "   ⏳ Envoi de la requête (peut prendre 30-60 secondes)...\n";

$client = HttpClient::create();

try {
    $response = $client->request('POST', 
        "https://router.huggingface.co/v1/chat/completions", 
        [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $model . ':fastest',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Réponds en français en une phrase: Bonjour, comment vas-tu?'
                    ]
                ],
                'max_tokens' => 100,
                'temperature' => 0.7,
            ],
            'timeout' => 60,
        ]
    );

    $statusCode = $response->getStatusCode();
    
    if ($statusCode === 200) {
        $data = $response->toArray();
        $generatedText = $data['choices'][0]['message']['content'] ?? 'Pas de réponse';
        
        echo "\n";
        echo "   ╔════════════════════════════════════════════════════════╗\n";
        echo "   ║  ✅ SUCCÈS! L'API FONCTIONNE PARFAITEMENT!            ║\n";
        echo "   ╚════════════════════════════════════════════════════════╝\n\n";
        echo "   📝 Réponse du modèle:\n";
        echo "   " . str_repeat("─", 60) . "\n";
        echo "   " . wordwrap($generatedText, 56, "\n   ") . "\n";
        echo "   " . str_repeat("─", 60) . "\n\n";
        echo "   🎉 Ton token est valide et l'API fonctionne!\n";
        echo "   🚀 Tu peux maintenant utiliser les rapports AI dans le dashboard admin!\n\n";
        
    } elseif ($statusCode === 401) {
        echo "\n   ❌ ERREUR 401: Token invalide ou expiré\n";
        echo "   📝 Actions à faire:\n";
        echo "      1. Va sur https://huggingface.co/settings/tokens\n";
        echo "      2. Crée un nouveau token avec la permission:\n";
        echo "         ✅ Make calls to Inference Providers\n";
        echo "      3. Copie le token dans .env.local\n";
        echo "      4. Relance ce script\n\n";
        
    } elseif ($statusCode === 403) {
        echo "\n   ❌ ERREUR 403: Accès refusé\n";
        echo "   📝 Ton token n'a pas la bonne permission.\n";
        echo "   Actions à faire:\n";
        echo "      1. Va sur https://huggingface.co/settings/tokens\n";
        echo "      2. Crée un nouveau token de type 'Fine-grained'\n";
        echo "      3. Coche la permission: Make calls to Inference Providers\n";
        echo "      4. Copie le nouveau token dans .env.local\n\n";
        
    } elseif ($statusCode === 503) {
        echo "\n   ⏳ ERREUR 503: Modèle en chargement\n";
        echo "   📝 C'est normal la première fois!\n";
        echo "   Actions à faire:\n";
        echo "      1. Attends 20-30 secondes\n";
        echo "      2. Relance ce script\n";
        echo "      3. Le modèle devrait être chargé\n\n";
        
    } else {
        $errorBody = $response->getContent(false);
        echo "\n   ❌ ERREUR $statusCode\n";
        echo "   Détails: $errorBody\n\n";
    }

} catch (\Exception $e) {
    echo "\n   ❌ EXCEPTION: " . $e->getMessage() . "\n\n";
}

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  TEST TERMINÉ                                              ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
