<div class="entete-page">
    <div>
        <h1>Employes jamais affectes</h1>
        <p><?= count($employes) ?> employe(s) concerne(s)</p>
    </div>
    <a href="<?= e(url('employes')) ?>" class="btn btn-secondaire">Retour</a>
</div>

<div class="carte">
    <?php if (empty($employes)): ?>
        <p class="vide">Tous les employes ont deja ete affectes au moins une fois.</p>
    <?php else: ?>
        <table class="tableau">
            <thead>
                <tr><th>Civilite</th><th>Nom</th><th>Prenom</th><th>Poste</th><th>Lieu actuel</th></tr>
            </thead>
            <tbody>
                <?php foreach ($employes as $employe): ?>
                    <tr>
                        <td><?= e($employe['civilite']) ?></td>
                        <td><?= e($employe['nom']) ?></td>
                        <td><?= e($employe['prenom']) ?></td>
                        <td><?= e($employe['poste']) ?></td>
                        <td><span class="badge badge-bleu"><?= e($employe['lieu_designation']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
