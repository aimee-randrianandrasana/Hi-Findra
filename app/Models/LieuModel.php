<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

// Acces aux donnees de la table "lieu"
final class LieuModel extends Model
{
    public function all(): array
    {
        return $this->db
            ->query('SELECT * FROM lieu ORDER BY designation ASC')
            ->fetchAll();
    }

    public function find(int $idLieu): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM lieu WHERE id_lieu = :id');
        $stmt->execute(['id' => $idLieu]);

        $lieu = $stmt->fetch();

        return $lieu === false ? null : $lieu;
    }

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

    public function delete(int $idLieu): bool
    {
        $stmt = $this->db->prepare('DELETE FROM lieu WHERE id_lieu = :id');

        return $stmt->execute(['id' => $idLieu]);
    }

    /** Recherche par designation ou province (LIKE). */
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

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM lieu')->fetchColumn();
    }

    /** Verifie l'unicite (designation + province), hors un eventuel id exclu. */
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

    /** Le lieu est-il utilise par au moins un employe ou une affectation ? */
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
