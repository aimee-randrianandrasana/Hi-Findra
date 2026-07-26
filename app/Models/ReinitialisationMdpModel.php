<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Gere les jetons de reinitialisation de mot de passe.
 * Le jeton transmis par email n'est jamais stocke en clair :
 * seul son hash SHA-256 (deterministe, donc recherchable) est persiste.
 */
final class ReinitialisationMdpModel extends Model
{
    public function creer(int $utilisateurId, string $jetonHash, string $expireLe): int
    {
        // On invalide les anciennes demandes pour eviter une accumulation de jetons actifs.
        $this->supprimerPourUtilisateur($utilisateurId);

        $stmt = $this->db->prepare(
            'INSERT INTO reinitialisation_mdp (utilisateur_id, jeton, expire_le)
             VALUES (:utilisateur_id, :jeton, :expire_le)'
        );

        $stmt->execute([
            'utilisateur_id' => $utilisateurId,
            'jeton'          => $jetonHash,
            'expire_le'      => $expireLe,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function trouverValide(string $jetonHash): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM reinitialisation_mdp
             WHERE jeton = :jeton AND utilise = 0 AND expire_le > NOW()'
        );
        $stmt->execute(['jeton' => $jetonHash]);

        $resultat = $stmt->fetch();

        return $resultat === false ? null : $resultat;
    }

    public function marquerUtilise(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE reinitialisation_mdp SET utilise = 1 WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }

    public function supprimerPourUtilisateur(int $utilisateurId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM reinitialisation_mdp WHERE utilisateur_id = :id');

        return $stmt->execute(['id' => $utilisateurId]);
    }
}
