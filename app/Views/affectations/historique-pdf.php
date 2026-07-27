<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 12px;
            line-height: 1.5;
        }

        .entete {
            text-align: center;
            margin-bottom: 30px;
        }

        .entete h1 {
            font-size: 18px;
            margin: 10px 0 0;
            text-transform: uppercase;
        }

        .entete .date {
            color: #64748b;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
        }

        th {
            background: #f1f5f9;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: .03em;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }

        .pied {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }

        .nb {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="entete">
        <h1>Historique des affectations supprimees</h1>
        <?php if (!empty($affectations)): ?>
            <div class="date">
                Historique d'affectation depuis le <?= e(date('d/m/Y', strtotime($dateMin))) ?> jusqu'au <?= e(date('d/m/Y', strtotime($dateMax))) ?>
            </div>
        <?php endif; ?>
        <div class="date">Imprime le <?= e(date('d/m/Y a H:i')) ?></div>
    </div>

    <p class="nb"><?= count($affectations) ?> affectation(s) dans l'historique</p>

    <?php if (!empty($affectations)): ?>
    <table>
        <thead>
            <tr>
                <th>N° Arrete</th>
                <th>Employe</th>
                <th>Poste</th>
                <th>Ancien lieu</th>
                <th>Nouveau lieu</th>
                <th>Raison</th>
                <th>Date arrete</th>
                <th>Prise de service</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($affectations as $a): ?>
            <tr>
                <td><?= e($a['numero_arrete']) ?></td>
                <td><?= e($a['employe_nom'] . ' ' . $a['employe_prenom']) ?></td>
                <td><?= e($a['poste']) ?></td>
                <td><?= e($a['ancien_lieu_designation'] ?? '—') ?></td>
                <td><?= e($a['nouveau_lieu_designation']) ?></td>
                <td><?= e($a['raison'] ?? '—') ?></td>
                <td><?= e(date('d/m/Y', strtotime($a['date_affect']))) ?></td>
                <td><?= e(date('d/m/Y', strtotime($a['date_prise_service']))) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p style="text-align:center;color:#64748b;margin-top:40px">Aucune affectation dans l'historique.</p>
    <?php endif; ?>

    <div class="pied">Document genere automatiquement — <?= e(app_name()) ?></div>
</body>
</html>
