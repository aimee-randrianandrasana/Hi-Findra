<div class="entete-page">
    <div>
        <h1>Historique des affectations</h1>
        <p><?= e($employe['civilite']) ?> <?= e($employe['nom']) ?> <?= e($employe['prenom']) ?> — <?= e($employe['poste']) ?></p>
    </div>
    <a href="<?= e(url('employes')) ?>" class="btn btn-secondaire">Retour</a>
</div>

<div class="carte">
    <?php if (empty($affectations)): ?>
        <p class="vide">Cet employe n'a jamais ete affecte.</p>
    <?php else: ?>
        <table class="tableau">
            <thead>
                <tr>
                    <th>N° Arrete</th>
                    <th>Ancien lieu</th>
                    <th>Nouveau lieu</th>
                    <th>Date arrete</th>
                    <th>Prise de service</th>
                    <th>Notifie</th>
                    <th>PDF</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($affectations as $a): ?>
                    <tr>
                        <td><?= e($a['numero_arrete']) ?></td>
                        <td><?= e($a['ancien_lieu_designation'] ?? '—') ?></td>
                        <td><?= e($a['nouveau_lieu_designation']) ?></td>
                        <td><?= e(date('d/m/Y', strtotime($a['date_affect']))) ?></td>
                        <td><?= e(date('d/m/Y', strtotime($a['date_prise_service']))) ?></td>
                        <td>
                            <?php if ($a['notifie_par_mail']): ?>
                                <span class="badge badge-vert">Oui</span>
                            <?php else: ?>
                                <span class="badge badge-gris">Non</span>
                            <?php endif; ?>
                        </td>
                        <td><a href="<?= e(url('affectations/' . $a['num_affect'] . '/pdf')) ?>" class="btn btn-secondaire btn-sm" target="_blank">Telecharger</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
