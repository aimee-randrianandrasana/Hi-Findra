<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\JetonConnexionModel;
use App\Models\UtilisateurModel;

// Bloque l'acces sans connexion, gere inactivite et remember-me
final class AuthMiddleware
{
    public function handle(): void
    {
        $config = require dirname(__DIR__, 2) . '/config/config.php';

        if (isset($_SESSION['user'])) {
            $this->verifierInactivite($config['security']['session_lifetime']);

            return;
        }

        if ($this->tenterReconnexionAutomatique()) {
            return;
        }

        header('Location: ' . url('connexion'));
        exit;
    }

    private function verifierInactivite(int $dureeMinutes): void
    {
        $derniereActivite = $_SESSION['derniere_activite'] ?? time();

        if (time() - $derniereActivite > $dureeMinutes * 60) {
            session_unset();
            session_destroy();
            flash('erreur', 'Votre session a expire par inactivite. Veuillez vous reconnecter.');
            header('Location: ' . url('connexion'));
            exit;
        }

        $_SESSION['derniere_activite'] = time();
    }

    private function tenterReconnexionAutomatique(): bool
    {
        if (empty($_COOKIE['remember_token']) || !str_contains($_COOKIE['remember_token'], ':')) {
            return false;
        }

        [$selecteur, $validateur] = explode(':', $_COOKIE['remember_token'], 2);

        $jetonModel = new JetonConnexionModel();
        $jeton = $jetonModel->trouverParSelecteur($selecteur);

        if ($jeton === null || !hash_equals($jeton['validateur_hash'], hash('sha256', $validateur))) {
            setcookie('remember_token', '', time() - 3600, '/');

            return false;
        }

        $utilisateur = (new UtilisateurModel())->find((int) $jeton['utilisateur_id']);

        if ($utilisateur === null || $utilisateur['statut'] !== 'actif') {
            return false;
        }

        $_SESSION['user'] = [
            'id'    => $utilisateur['id'],
            'nom'   => $utilisateur['nom'],
            'prenom'=> $utilisateur['prenom'],
            'email' => $utilisateur['email'],
            'role'  => $utilisateur['role'],
            'photo' => $utilisateur['photo'],
        ];
        $_SESSION['derniere_activite'] = time();

        return true;
    }
}
