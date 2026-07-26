<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

// Modele de base - fournit la connexion PDO a tous les modeles
abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}
