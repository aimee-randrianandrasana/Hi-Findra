<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

// Acces aux donnees de la table "affecter"
final class AffectationModel extends Model
{
    private const SELECT_DETAILLE = "
        SELECT
            a.*,
            e.nom AS employe_nom, e.prenom AS employe_prenom, e.civilite, e.poste, e.mail AS employe_mail, e.photo AS employe_photo,
            al.designation AS ancien_lieu_designation,
            nl.designation AS nouveau_lieu_designation
        FROM affecter a
        INNER JOIN employe e ON e.num_emp = a.num_emp
        LEFT JOIN lieu al ON al.id_lieu = a.ancien_lieu_id
        INNER JOIN lieu nl ON nl.id_lieu = a.nouveau_lieu_id
    ";

    public function find(int $numAffect): ?array
    {
        $stmt = $this->db->prepare(self::SELECT_DETAILLE . ' WHERE a.num_affect = :id');
        $stmt->execute(['id' => $numAffect]);

        $affectation = $stmt->fetch();

        return $affectation === false ? null : $affectation;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO affecter
                (numero_arrete, num_emp, ancien_lieu_id, nouveau_lieu_id, date_affect, date_prise_service)
             VALUES
                (:numero_arrete, :num_emp, :ancien_lieu_id, :nouveau_lieu_id, :date_affect, :date_prise_service)'
        );

        $stmt->execute([
            'numero_arrete'      => $data['numero_arrete'],
            'num_emp'            => $data['num_emp'],
            'ancien_lieu_id'     => $data['ancien_lieu_id'] ?: null,
            'nouveau_lieu_id'    => $data['nouveau_lieu_id'],
            'date_affect'        => $data['date_affect'],
            'date_prise_service' => $data['date_prise_service'],
        ]);

        // Le trigger "trg_affecter_after_insert" met a jour employe.id_lieu.
        return (int) $this->db->lastInsertId();
    }

    public function update(int $numAffect, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE affecter
             SET numero_arrete = :numero_arrete, date_affect = :date_affect,
                 date_prise_service = :date_prise_service
             WHERE num_affect = :id'
        );

        return $stmt->execute([
            'numero_arrete'      => $data['numero_arrete'],
            'date_affect'        => $data['date_affect'],
            'date_prise_service' => $data['date_prise_service'],
            'id'                 => $numAffect,
        ]);
    }

    public function delete(int $numAffect): bool
    {
        $stmt = $this->db->prepare('DELETE FROM affecter WHERE num_affect = :id');

        return $stmt->execute(['id' => $numAffect]);
    }

    /** Derniere affectation la plus recente d'un employe (hors une affectation donnee). */
    public function derniereAffectation(int $numEmp, ?int $excludeId = null): ?array
    {
        $sql = 'SELECT * FROM affecter WHERE num_emp = :numEmp';
        $params = ['numEmp' => $numEmp];

        if ($excludeId !== null) {
            $sql .= ' AND num_affect != :excludeId';
            $params['excludeId'] = $excludeId;
        }

        $sql .= ' ORDER BY date_affect DESC, num_affect DESC LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /** Historique complet des affectations d'un employe, du plus recent au plus ancien. */
    public function historiqueParEmploye(int $numEmp): array
    {
        $stmt = $this->db->prepare(
            self::SELECT_DETAILLE . ' WHERE a.num_emp = :numEmp ORDER BY a.date_affect DESC'
        );
        $stmt->execute(['numEmp' => $numEmp]);

        return $stmt->fetchAll();
    }

    /** Affectations dont la date d'arrete est comprise entre deux dates (incluses). */
    public function entreDeuxDates(string $dateDebut, string $dateFin): array
    {
        $stmt = $this->db->prepare(
            self::SELECT_DETAILLE . '
            WHERE a.date_affect BETWEEN :debut AND :fin
            ORDER BY a.date_affect DESC'
        );
        $stmt->execute(['debut' => $dateDebut, 'fin' => $dateFin]);

        return $stmt->fetchAll();
    }

    public function prochainNumeroArrete(): string
    {
        $stmt = $this->db->query('SELECT MAX(CAST(numero_arrete AS UNSIGNED)) FROM affecter');
        $max = (int) $stmt->fetchColumn();

        return str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }

    public function existeDejaNumeroArrete(string $numeroArrete, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM affecter WHERE numero_arrete = :numero';
        $params = ['numero' => $numeroArrete];

        if ($excludeId !== null) {
            $sql .= ' AND num_affect != :excludeId';
            $params['excludeId'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function marquerNotifiee(int $numAffect): bool
    {
        $stmt = $this->db->prepare('UPDATE affecter SET notifie_par_mail = 1 WHERE num_affect = :id');

        return $stmt->execute(['id' => $numAffect]);
    }

    public function paginate(int $page, int $perPage = 10): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            self::SELECT_DETAILLE . ' ORDER BY a.date_affect DESC LIMIT :limit OFFSET :offset'
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
        return (int) $this->db->query('SELECT COUNT(*) FROM affecter')->fetchColumn();
    }

}
