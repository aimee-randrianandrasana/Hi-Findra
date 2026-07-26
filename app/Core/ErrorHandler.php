<?php

declare(strict_types=1);

namespace App\Core;

// Centralise la gestion des erreurs et exceptions non interceptees
final class ErrorHandler
{
    public static function register(bool $debug): void
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '0'); // jamais d'erreurs brutes a l'ecran

        set_error_handler(function (int $level, string $message, string $file = '', int $line = 0) {
            Logger::error("{$message} dans {$file} ligne {$line}");
            return true;
        });

        set_exception_handler(function (\Throwable $e) use ($debug) {
            Logger::error($e->getMessage() . ' dans ' . $e->getFile() . ' ligne ' . $e->getLine());

            http_response_code(500);

            if ($debug) {
                echo '<pre style="padding:20px;background:#1e1e1e;color:#f5f5f5;font-family:monospace">';
                echo e($e->getMessage()) . "\n\n" . e($e->getTraceAsString());
                echo '</pre>';
            } else {
                require dirname(__DIR__) . '/Views/errors/500.php';
            }
        });
    }
}
