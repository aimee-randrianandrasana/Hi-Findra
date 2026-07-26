<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Gere les jetons "Se souvenir de moi".
 * Pattern selecteur/validateur : le selecteur permet une recherche
 * directe en base, le validateur (secret) n'est jamais stocke en clair.
 */
final class JetonConnexionModel extends Model
{
    public function creer(int $utilisateurId, string $selecteur, string $validateurHash, string $expireLe): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO jeton_connexion (utilisateur_id, selecteur, validateur_hash, expire_le)
             VALUES (:utilisateur_id, :selecteur, :validateur_hash, :expire_le)'
        );

        $stmt->execute([
            'utilisateur_id'  => $utilisateurId,
            'selecteur'       => $selecteur,
            'validateur_hash' => $validateurHash,
            'expire_le'       => $expireLe,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function trouverParSelecteur(string $selecteur): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM jeton_connexion WHERE selecteur = :selecteur AND expire_le > NOW()'
        );
        $stmt->execute(['selecteur' => $selecteur]);

        $jeton = $stmt->fetch();

        return $jeton === false ? null : $jeton;
    }

    public function supprimer(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM jeton_connexion WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }

    public function supprimerPourUtilisateur(int $utilisateurId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM jeton_connexion WHERE utilisateur_id = :id');

        return $stmt->execute(['id' => $utilisateurId]);
    }
}
