<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

// Acces aux donnees de la table "utilisateur"
final class UtilisateurModel extends Model
{
    public function all(): array
    {
        return $this->db
            ->query('SELECT id, nom, prenom, email, photo, role, statut, date_creation
                      FROM utilisateur ORDER BY nom ASC, prenom ASC')
            ->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateur WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $utilisateur = $stmt->fetch();

        return $utilisateur === false ? null : $utilisateur;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateur WHERE email = :email');
        $stmt->execute(['email' => $email]);

        $utilisateur = $stmt->fetch();

        return $utilisateur === false ? null : $utilisateur;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, statut)
             VALUES (:nom, :prenom, :email, :mot_de_passe, :role, :statut)'
        );

        $stmt->execute([
            'nom'          => $data['nom'],
            'prenom'      => $data['prenom'],
            'email'        => $data['email'],
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
            'role'         => $data['role'] ?? 'gestionnaire',
            'statut'       => $data['statut'] ?? 'actif',
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** Met a jour les informations de profil (hors mot de passe et photo). */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email, role = :role
             WHERE id = :id'
        );

        return $stmt->execute([
            'nom'    => $data['nom'],
            'prenom' => $data['prenom'],
            'email'  => $data['email'],
            'role'   => $data['role'],
            'id'     => $id,
        ]);
    }

    public function updateMotDePasse(int $id, string $motDePasseClair): bool
    {
        $stmt = $this->db->prepare('UPDATE utilisateur SET mot_de_passe = :mdp WHERE id = :id');

        return $stmt->execute([
            'mdp' => password_hash($motDePasseClair, PASSWORD_DEFAULT),
            'id'  => $id,
        ]);
    }

    public function updatePhoto(int $id, string $cheminPhoto): bool
    {
        $stmt = $this->db->prepare('UPDATE utilisateur SET photo = :photo WHERE id = :id');

        return $stmt->execute(['photo' => $cheminPhoto, 'id' => $id]);
    }

    public function changerStatut(int $id, string $statut): bool
    {
        $stmt = $this->db->prepare('UPDATE utilisateur SET statut = :statut WHERE id = :id');

        return $stmt->execute(['statut' => $statut, 'id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM utilisateur WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }

    public function search(string $terme): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, nom, prenom, email, photo, role, statut FROM utilisateur
             WHERE nom LIKE :terme OR prenom LIKE :terme OR email LIKE :terme
             ORDER BY nom ASC'
        );

        $stmt->execute(['terme' => "%{$terme}%"]);

        return $stmt->fetchAll();
    }

    public function paginate(int $page, int $perPage = 10): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            'SELECT id, nom, prenom, email, photo, role, statut, date_creation FROM utilisateur
             ORDER BY nom ASC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'  => $stmt->fetchAll(),
            'total' => $this->countAll(),
            'page'  => $page,
            'pages' => (int) ceil($this->countAll() / $perPage),
        ];
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM utilisateur')->fetchColumn();
    }

    public function existeDejaEmail(string $email, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM utilisateur WHERE email = :email';
        $params = ['email' => $email];

        if ($excludeId !== null) {
            $sql .= ' AND id != :excludeId';
            $params['excludeId'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }
}
