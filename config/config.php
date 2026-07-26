<?php

declare(strict_types=1);

use App\Core\Env;

Env::load(dirname(__DIR__) . '/.env');

return [
    'app' => [
        'name'  => Env::get('APP_NAME', 'Gestion des Affectations'),
        'url'   => rtrim((string) Env::get('APP_URL', 'http://localhost'), '/'),
        'env'   => Env::get('APP_ENV', 'production'),
        'debug' => filter_var(Env::get('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    ],

    'database' => [
        'host'    => Env::get('DB_HOST', '127.0.0.1'),
        'port'    => Env::get('DB_PORT', '3306'),
        'name'    => Env::get('DB_NAME', 'gestion_affectations'),
        'user'    => Env::get('DB_USER', 'root'),
        'pass'    => Env::get('DB_PASS', ''),
        'charset' => 'utf8mb4',
    ],

    'mail' => [
        'host'        => Env::get('MAIL_HOST'),
        'port'        => (int) Env::get('MAIL_PORT', 587),
        'username'    => Env::get('MAIL_USERNAME'),
        'password'    => Env::get('MAIL_PASSWORD'),
        'encryption'  => Env::get('MAIL_ENCRYPTION', 'tls'),
        'from_address'=> Env::get('MAIL_FROM_ADDRESS'),
        'from_name'   => Env::get('MAIL_FROM_NAME'),
    ],

    'security' => [
        'session_lifetime'  => (int) Env::get('SESSION_LIFETIME', 120),
        'remember_me_days'  => (int) Env::get('REMEMBER_ME_DAYS', 20),
        'max_login_attempts'=> (int) Env::get('MAX_LOGIN_ATTEMPTS', 5),
        'lockout_minutes'   => (int) Env::get('LOCKOUT_MINUTES', 5),
    ],

    'paths' => [
        'root'    => dirname(__DIR__),
        'uploads' => dirname(__DIR__) . '/public/uploads',
        'logs'    => dirname(__DIR__) . '/storage/logs',
    ],
];
