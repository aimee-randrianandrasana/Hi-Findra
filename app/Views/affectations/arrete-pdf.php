<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 13px;
            line-height: 1.7;
        }

        .entete {
            text-align: center;
            margin-bottom: 40px;
        }

        .entete .organisme {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .entete h1 {
            font-size: 18px;
            margin: 10px 0 0;
            text-transform: uppercase;
        }

        .reference {
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 30px;
        }

        .corps {
            text-align: justify;
            margin: 0 60px 40px;
        }

        .signature {
            text-align: right;
            margin: 60px 60px 0;
        }

        .pied {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="entete">
        <div class="organisme"><?= e(app_name()) ?></div>
        <h1>Arrete d'affectation</h1>
    </div>

    <p class="reference">
        Arrete N° <?= e($affectation['numero_arrete']) ?> du <?= e(date('d/m/Y', strtotime($affectation['date_affect']))) ?>
    </p>

    <div class="corps">
        <p>
            <?= e($affectation['civilite']) ?> <?= e($affectation['employe_nom']) ?> <?= e($affectation['employe_prenom']) ?>,
            qui occupe le poste de <?= e($affectation['poste']) ?>
            <?= $affectation['ancien_lieu_designation'] ? ' a ' . e($affectation['ancien_lieu_designation']) : '' ?>,
            est affecte(e) a <strong><?= e($affectation['nouveau_lieu_designation']) ?></strong>
            pour compter de la date de prise de service du
            <strong><?= e(date('d/m/Y', strtotime($affectation['date_prise_service']))) ?></strong>.
        </p>
        <?php if (!empty($affectation['raison'])): ?>
        <p>
            <strong>Motif :</strong> <?= e($affectation['raison']) ?>
        </p>
        <?php endif; ?>
        <p>
            Le present communique sera enregistre et communique partout ou besoin sera.
        </p>
    </div>

    <div class="signature">
        Fait le <?= e(date('d/m/Y')) ?><br><br>
        Le Responsable des Ressources Humaines
    </div>

    <div class="pied">Document genere automatiquement — <?= e(app_name()) ?></div>
</body>
</html>
