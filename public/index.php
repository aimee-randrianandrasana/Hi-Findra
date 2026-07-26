<?php

declare(strict_types=1);

use App\Core\ErrorHandler;
use App\Core\Router;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/app/Helpers/functions.php';

$config = require dirname(__DIR__) . '/config/config.php';

ErrorHandler::register($config['app']['debug']);

// --- Session securisee ---
session_set_cookie_params([
    'lifetime' => $config['security']['session_lifetime'] * 60,
    'path'     => '/',
    'secure'   => !empty($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// --- Chargement des routes ---
$router = new Router();
require dirname(__DIR__) . '/config/routes.php';

// --- Dispatch de la requete courante ---
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
