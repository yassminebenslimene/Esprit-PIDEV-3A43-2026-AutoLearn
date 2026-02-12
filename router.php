<?php

// Router pour le serveur PHP intégré
// Ce fichier permet de gérer correctement le routing Symfony

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Si c'est un fichier statique qui existe dans public/, le servir directement
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false; // Le serveur PHP servira le fichier directement
}

// Pour les assets (CSS, JS, images)
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $uri)) {
    // Vérifier dans public/
    $file = __DIR__ . '/public' . $uri;
    if (file_exists($file)) {
        return false;
    }
    // Vérifier dans public/Backoffice/
    $file = __DIR__ . '/public/Backoffice' . $uri;
    if (file_exists($file)) {
        return false;
    }
    // Vérifier dans public/frontoffice/
    $file = __DIR__ . '/public/frontoffice' . $uri;
    if (file_exists($file)) {
        return false;
    }
}

// Sinon, passer par index.php pour le routing Symfony
require __DIR__ . '/public/index.php';
