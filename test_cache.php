<?php

// Script de test pour vérifier les headers de cache HTTP
$url = 'http://localhost:8000/'; // Ajustez l'URL selon votre configuration

echo "Test des headers de cache HTTP pour : $url\n\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: Test Cache Script',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        ]
    ]
]);

$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "Erreur : Impossible d'accéder à l'URL\n";
    echo "Assurez-vous que le serveur Symfony est démarré avec : php -S localhost:8000 -t public\n";
    exit(1);
}

// Récupérer les headers de réponse
$headers = $http_response_header ?? [];

echo "Headers de réponse :\n";
echo "==================\n";

$cacheHeaders = [];
foreach ($headers as $header) {
    echo $header . "\n";
    
    // Filtrer les headers de cache
    if (stripos($header, 'cache-control') !== false ||
        stripos($header, 'etag') !== false ||
        stripos($header, 'last-modified') !== false ||
        stripos($header, 'expires') !== false) {
        $cacheHeaders[] = $header;
    }
}

echo "\nHeaders de cache trouvés :\n";
echo "=========================\n";
if (empty($cacheHeaders)) {
    echo "Aucun header de cache trouvé.\n";
    echo "Cela peut indiquer que :\n";
    echo "1. Le cache HTTP n'est pas activé\n";
    echo "2. Vous êtes en mode debug (dev)\n";
    echo "3. Les annotations de cache ne sont pas prises en compte\n";
} else {
    foreach ($cacheHeaders as $header) {
        echo $header . "\n";
    }
}

echo "\nPour tester en mode production :\n";
echo "1. Mettez APP_ENV=prod dans .env.local\n";
echo "2. Videz le cache : php bin/console cache:clear --env=prod\n";
echo "3. Relancez le serveur\n";
