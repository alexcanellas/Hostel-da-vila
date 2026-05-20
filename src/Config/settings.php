<?php

declare(strict_types=1);

return [
    'app' => [
        'env'   => $_ENV['APP_ENV']  ?? 'development',
        'debug' => (bool) ($_ENV['APP_DEBUG'] ?? false),
        'name'  => $_ENV['APP_NAME'] ?? 'Hostel da Vila API',
        'url'   => $_ENV['APP_URL']  ?? 'http://localhost:8080',
    ],

    'jwt' => [
        'secret'     => $_ENV['JWT_SECRET']     ?? 'change_me',
        'expiration' => (int) ($_ENV['JWT_EXPIRATION'] ?? 86400),
        'algorithm'  => 'HS256',
    ],

    'database' => [
        'host'     => $_ENV['DB_HOST']     ?? 'db',
        'port'     => (int) ($_ENV['DB_PORT'] ?? 3306),
        'dbname'   => $_ENV['DB_DATABASE'] ?? 'hosteldavila',
        'username' => $_ENV['DB_USERNAME'] ?? 'hosteldavila',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset'  => 'utf8mb4',
        'options'  => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ],
    ],

    'redis' => [
        'host'     => $_ENV['REDIS_HOST']     ?? 'redis',
        'port'     => (int) ($_ENV['REDIS_PORT'] ?? 6379),
        'password' => $_ENV['REDIS_PASSWORD'] ?? null,
    ],

    'cloudbeds' => [
        'client_id'      => $_ENV['CLOUDBEDS_CLIENT_ID']      ?? '',
        'client_secret'  => $_ENV['CLOUDBEDS_CLIENT_SECRET']  ?? '',
        'redirect_uri'   => $_ENV['CLOUDBEDS_REDIRECT_URI']   ?? '',
        'property_id'    => $_ENV['CLOUDBEDS_PROPERTY_ID']    ?? '',
        'webhook_secret' => $_ENV['CLOUDBEDS_WEBHOOK_SECRET'] ?? '',
    ],

    'getnet' => [
        'client_id'     => $_ENV['GETNET_CLIENT_ID']     ?? '',
        'client_secret' => $_ENV['GETNET_CLIENT_SECRET'] ?? '',
        'seller_id'     => $_ENV['GETNET_SELLER_ID']     ?? '',
        'env'           => $_ENV['GETNET_ENV']            ?? 'sandbox',
        'base_url'      => ($_ENV['GETNET_ENV'] ?? 'sandbox') === 'production'
            ? 'https://api.getnet.com.br'
            : 'https://api-sandbox.getnet.com.br',
    ],

    'kommo' => [
        'client_id'      => $_ENV['KOMMO_CLIENT_ID']      ?? '',
        'client_secret'  => $_ENV['KOMMO_CLIENT_SECRET']  ?? '',
        'subdomain'      => $_ENV['KOMMO_SUBDOMAIN']      ?? '',
        'redirect_uri'   => $_ENV['KOMMO_REDIRECT_URI']   ?? '',
        'webhook_secret' => $_ENV['KOMMO_WEBHOOK_SECRET'] ?? '',
    ],

    'hsystem' => [
        'api_url'  => $_ENV['HSYSTEM_API_URL']  ?? '',
        'api_key'  => $_ENV['HSYSTEM_API_KEY']  ?? '',
        'hotel_id' => $_ENV['HSYSTEM_HOTEL_ID'] ?? '',
    ],

    'log' => [
        'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
        'path'  => $_ENV['LOG_PATH']  ?? __DIR__ . '/../../storage/logs/app.log',
    ],
];
