<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Enregistre et analyse les tentatives de connexion afin de
 * detecter et bloquer les attaques par force brute.
 */
final class TentativeConnexionModel extends Model
{
    public function enregistrer(string $email, string $adresseIp, bool $reussie): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO tentative_connexion (email, adresse_ip, reussie) VALUES (:email, :ip, :reussie)'
        );

        $stmt->execute([
            'email'   => $email,
            'ip'      => $adresseIp,
            'reussie' => $reussie ? 1 : 0,
        ]);
    }

    /** Nombre d'echecs recents (email OU IP) sur la fenetre de verrouillage. */
    public function nombreEchecsRecents(string $email, string $adresseIp, int $minutes): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM tentative_connexion
             WHERE reussie = 0
               AND tentee_le > (NOW() - INTERVAL :minutes MINUTE)
               AND (email = :email OR adresse_ip = :ip)'
        );

        $stmt->execute(['minutes' => $minutes, 'email' => $email, 'ip' => $adresseIp]);

        return (int) $stmt->fetchColumn();
    }

    /** Purge les tentatives anciennes (a appeler periodiquement, ex: tache planifiee). */
    public function purgerAnciennes(int $joursConservation = 30): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM tentative_connexion WHERE tentee_le < (NOW() - INTERVAL :jours DAY)'
        );
        $stmt->execute(['jours' => $joursConservation]);
    }
}
