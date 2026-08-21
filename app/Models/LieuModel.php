<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

// Acces aux donnees de la table "lieu"
final class LieuModel extends Model
{
    // Retourne tous les lieux tries par designation.
    public function all(): array
    {
        return $this->db
            ->query('SELECT * FROM lieu ORDER BY designation ASC')
            ->fetchAll();
    }

    // Retourne un lieu par son identifiant.
    public function find(int $idLieu): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM lieu WHERE id_lieu = :id');
        $stmt->execute(['id' => $idLieu]);

        $lieu = $stmt->fetch();

        return $lieu === false ? null : $lieu;
    }

    // Cree un nouveau lieu et retourne son identifiant.
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO lieu (designation, province) VALUES (:designation, :province)'
        );

        $stmt->execute([
            'designation' => $data['designation'],
            'province'    => $data['province'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    // Met a jour un lieu existant.
    public function update(int $idLieu, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE lieu SET designation = :designation, province = :province WHERE id_lieu = :id'
        );

        return $stmt->execute([
            'designation' => $data['designation'],
            'province'    => $data['province'],
            'id'          => $idLieu,
        ]);
    }

    // Supprime un lieu de la base.
    public function delete(int $idLieu): bool
    {
        $stmt = $this->db->prepare('DELETE FROM lieu WHERE id_lieu = :id');

        return $stmt->execute(['id' => $idLieu]);
    }

    // Recherche des lieux par designation ou province (LIKE).
    public function search(string $terme): array
    {
        $safe = '%' . addcslashes($terme, '%_') . '%';

        $stmt = $this->db->prepare(
            'SELECT * FROM lieu
             WHERE designation LIKE :terme OR province LIKE :terme
             ORDER BY designation ASC'
        );

        $stmt->execute(['terme' => $safe]);

        return $stmt->fetchAll();
    }

    // Retourne les lieux pagines.
    public function paginate(int $page, int $perPage = 10): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            'SELECT * FROM lieu ORDER BY designation ASC LIMIT :limit OFFSET :offset'
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

    // Nombre total de lieux.
    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM lieu')->fetchColumn();
    }

    // Verifie l'unicite d'un couple designation/province (hors id exclu).
    public function existeDejaDesignationProvince(string $designation, string $province, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM lieu WHERE designation = :designation AND province = :province';
        $params = ['designation' => $designation, 'province' => $province];

        if ($excludeId !== null) {
            $sql .= ' AND id_lieu != :excludeId';
            $params['excludeId'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    // Verifie si le lieu est reference par un employe ou une affectation.
    public function estUtilise(int $idLieu): bool
    {
        $stmt = $this->db->prepare(
            'SELECT
                (SELECT COUNT(*) FROM employe WHERE id_lieu = :id1) +
                (SELECT COUNT(*) FROM affecter WHERE ancien_lieu_id = :id2 OR nouveau_lieu_id = :id3)
             AS total'
        );
        $stmt->execute(['id1' => $idLieu, 'id2' => $idLieu, 'id3' => $idLieu]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
