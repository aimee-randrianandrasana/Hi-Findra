<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

// Connexion unique a la base de donnees (Singleton)
final class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
        // Empeche l'instanciation directe.
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require dirname(__DIR__, 2) . '/config/config.php';
            $db = $config['database'];

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $db['host'],
                $db['port'],
                $db['name'],
                $db['charset']
            );

            try {
                self::$instance = new PDO($dsn, $db['user'], $db['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                Logger::error('Connexion BDD impossible : ' . $e->getMessage());
                throw new PDOException('Impossible de se connecter a la base de donnees.');
            }
        }

        return self::$instance;
    }

    private function __clone()
    {
        // Empeche le clonage de l'instance.
    }
}
