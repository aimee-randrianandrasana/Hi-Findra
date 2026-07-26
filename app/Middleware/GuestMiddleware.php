<?php

declare(strict_types=1);

namespace App\Middleware;

// Empeche un utilisateur connecte d'acceder aux pages publiques
final class GuestMiddleware
{
    public function handle(): void
    {
        if (isset($_SESSION['user'])) {
            header('Location: ' . url('accueil'));
            exit;
        }
    }
}
