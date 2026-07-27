<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

// Acces aux donnees de la table "employe"
final class EmployeModel extends Model
{
    private const SELECT_AVEC_LIEU = '
        SELECT e.*, l.designation AS lieu_designation, l.province AS lieu_province
        FROM employe e
        INNER JOIN lieu l ON l.id_lieu = e.id_lieu
    ';

    public function all(): array
    {
        return $this->db
            ->query(self::SELECT_AVEC_LIEU . ' ORDER BY e.nom ASC, e.prenom ASC')
            ->fetchAll();
    }

    public function find(int $numEmp): ?array
    {
        $stmt = $this->db->prepare(self::SELECT_AVEC_LIEU . ' WHERE e.num_emp = :numEmp');
        $stmt->execute(['numEmp' => $numEmp]);

        $employe = $stmt->fetch();

        return $employe === false ? null : $employe;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO employe (civilite, nom, prenom, mail, poste, photo, id_lieu)
             VALUES (:civilite, :nom, :prenom, :mail, :poste, :photo, :id_lieu)'
        );

        $stmt->execute([
            'civilite' => $data['civilite'],
            'nom'      => $data['nom'],
            'prenom'   => $data['prenom'],
            'mail'     => $data['mail'],
            'poste'    => $data['poste'],
            'photo'    => $data['photo'] ?? null,
            'id_lieu'  => $data['id_lieu'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $numEmp, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE employe
             SET civilite = :civilite, nom = :nom, prenom = :prenom,
                 mail = :mail, poste = :poste
             WHERE num_emp = :numEmp'
        );

        return $stmt->execute([
            'civilite' => $data['civilite'],
            'nom'      => $data['nom'],
            'prenom'   => $data['prenom'],
            'mail'     => $data['mail'],
            'poste'    => $data['poste'],
            'numEmp'   => $numEmp,
        ]);
    }

    public function delete(int $numEmp): bool
    {
        $stmt = $this->db->prepare('DELETE FROM employe WHERE num_emp = :numEmp');

        return $stmt->execute(['numEmp' => $numEmp]);
    }

    /** Recherche par nom OU prenom (LIKE %terme%), comme demande au sujet. */
    public function search(string $terme): array
    {
        $safe = '%' . addcslashes($terme, '%_') . '%';

        $stmt = $this->db->prepare(
            self::SELECT_AVEC_LIEU . '
            WHERE e.nom LIKE :terme OR e.prenom LIKE :terme
            ORDER BY e.nom ASC, e.prenom ASC'
        );

        $stmt->execute(['terme' => $safe]);

        return $stmt->fetchAll();
    }

    public function paginate(int $page, int $perPage = 10): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            self::SELECT_AVEC_LIEU . ' ORDER BY e.nom ASC LIMIT :limit OFFSET :offset'
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
        return (int) $this->db->query('SELECT COUNT(*) FROM employe')->fetchColumn();
    }

    public function existeDejaMail(string $mail, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM employe WHERE mail = :mail';
        $params = ['mail' => $mail];

        if ($excludeId !== null) {
            $sql .= ' AND num_emp != :excludeId';
            $params['excludeId'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function updatePhoto(int $numEmp, string $cheminPhoto): bool
    {
        $stmt = $this->db->prepare('UPDATE employe SET photo = :photo WHERE num_emp = :numEmp');

        return $stmt->execute(['photo' => $cheminPhoto, 'numEmp' => $numEmp]);
    }

    public function updateLieu(int $numEmp, int $idLieu): bool
    {
        $stmt = $this->db->prepare('UPDATE employe SET id_lieu = :idLieu WHERE num_emp = :numEmp');

        return $stmt->execute(['idLieu' => $idLieu, 'numEmp' => $numEmp]);
    }

    /** Employes n'apparaissant dans aucune ligne de la table "affecter". */
    public function jamaisAffectes(): array
    {
        return $this->db->query(
            self::SELECT_AVEC_LIEU . '
            WHERE e.num_emp NOT IN (SELECT DISTINCT num_emp FROM affecter)
            ORDER BY e.nom ASC, e.prenom ASC'
        )->fetchAll();
    }

    public function nombreJamaisAffectes(): int
    {
        return (int) $this->db->query(
            'SELECT COUNT(*) FROM employe
             WHERE num_emp NOT IN (SELECT DISTINCT num_emp FROM affecter)'
        )->fetchColumn();
    }
}
