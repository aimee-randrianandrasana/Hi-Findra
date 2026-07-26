<?php

declare(strict_types=1);

namespace App\Core;

// Journalisation simple des erreurs dans storage/logs
final class Logger
{
    private static function logPath(): string
    {
        $dir = dirname(__DIR__, 2) . '/storage/logs';

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        return $dir . '/' . date('Y-m-d') . '.log';
    }

    public static function error(string $message): void
    {
        self::write('ERROR', $message);
    }

    public static function warning(string $message): void
    {
        self::write('WARNING', $message);
    }

    public static function info(string $message): void
    {
        self::write('INFO', $message);
    }

    private static function write(string $level, string $message): void
    {
        $line = sprintf('[%s] %s: %s%s', date('Y-m-d H:i:s'), $level, $message, PHP_EOL);
        file_put_contents(self::logPath(), $line, FILE_APPEND | LOCK_EX);
    }
}
