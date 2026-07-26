<div class="entete-page">
    <div>
        <h1>Lieux</h1>
        <p><?= (int) $total ?> lieu(x) enregistre(s)</p>
    </div>
    <a href="<?= e(url('lieux/creer')) ?>" class="btn btn-primaire">+ Ajouter un lieu</a>
</div>

<div class="carte emp-layout">
    <div class="barre-outils">
        <form method="get" action="<?= e(url('lieux')) ?>" style="display: flex; gap: .5rem">
            <input type="search" name="q" value="<?= e($terme) ?>" placeholder="Rechercher par designation ou province..."
                   data-recherche-instantanee="#tableau-lieux">
            <button type="submit" class="btn btn-secondaire">Rechercher</button>
        </form>
    </div>

    <?php if (empty($lieux)): ?>
        <p class="vide">Aucun lieu trouve.</p>
    <?php else: ?>
        <div class="emp-container">
            <div class="emp-table-wrapper">
                <table class="tableau" id="tableau-lieux">
                    <thead>
                        <tr>
                            <th data-triable>Designation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lieux as $lieu): ?>
                            <tr class="emp-ligne"
                                data-id="<?= $lieu['id_lieu'] ?>"
                                data-designation="<?= e($lieu['designation']) ?>"
                                data-province="<?= e($lieu['province']) ?>"
                                data-url-edit="<?= e(url('lieux/' . $lieu['id_lieu'] . '/editer')) ?>"
                                data-url-delete="<?= e(url('lieux/' . $lieu['id_lieu'] . '/supprimer')) ?>">
                                <td><?= e($lieu['designation']) ?></td>
                                <td class="cellule-actions">
                                    <button class="btn btn-danger btn-sm" data-confirme="modale-suppr-<?= $lieu['id_lieu'] ?>">Supprimer</button>
                                </td>
                            </tr>

                            <div class="fond-modale" id="modale-suppr-<?= $lieu['id_lieu'] ?>">
                                <div class="modale">
                                    <h3>Confirmer la suppression</h3>
                                    <p>Supprimer le lieu <strong><?= e($lieu['designation']) ?></strong> ? Cette action est irreversible.</p>
                                    <div class="actions-modale">
                                        <button type="button" class="btn btn-secondaire" data-fermer-modale>Annuler</button>
                                        <form method="post" action="<?= e(url('lieux/' . $lieu['id_lieu'] . '/supprimer')) ?>">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <aside class="emp-panel" id="lieu-panel">
                <div class="emp-panel-vide" id="lieu-panel-vide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
                    <p>Cliquez sur un lieu<br>pour voir sa fiche</p>
                </div>
                <div class="emp-panel-content" id="lieu-panel-content" style="display: none">
                    <button type="button" class="emp-panel-fermer" id="lieu-panel-fermer" title="Fermer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <div class="emp-panel-entete">
                        <div style="width:48px;height:48px;border-radius:12px;background:var(--vert-pale);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg viewBox="0 0 24 24" fill="none" stroke="var(--vert)" stroke-width="2" width="22" height="22"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
                        </div>
                        <div>
                            <h3 id="lieu-panel-designation"></h3>
                        </div>
                    </div>
                    <dl class="emp-panel-details">
                        <dt>Province</dt>
                        <dd><span class="badge badge-bleu" id="lieu-panel-province"></span></dd>
                    </dl>
                    <div class="emp-panel-actions">
                        <a href="" class="btn btn-primaire" id="lieu-panel-edit">Modifier</a>
                    </div>
                </div>
            </aside>
        </div>

        <?php $base = 'lieux'; $params = $terme !== '' ? ['q' => $terme] : []; require dirname(__DIR__) . '/partials/pagination.php'; ?>
    <?php endif; ?>
</div>

<script>
(function() {
    var panelContent = document.getElementById('lieu-panel-content');
    var panelVide = document.getElementById('lieu-panel-vide');
    var panelFermer = document.getElementById('lieu-panel-fermer');

    var lignes = document.querySelectorAll('#tableau-lieux .emp-ligne');

    function remplir(ligne) {
        document.getElementById('lieu-panel-designation').textContent = ligne.dataset.designation;
        document.getElementById('lieu-panel-province').textContent = ligne.dataset.province;
        document.getElementById('lieu-panel-edit').href = ligne.dataset.urlEdit;
    }

    lignes.forEach(function(l) {
        l.addEventListener('click', function(e) {
            if (e.target.closest('.cellule-actions')) return;
            lignes.forEach(function(x) { x.classList.remove('emp-ligne-active'); });
            l.classList.add('emp-ligne-active');
            remplir(l);
            panelVide.style.display = 'none';
            panelContent.style.display = '';
        });
    });

    panelFermer.addEventListener('click', function() {
        panelContent.style.display = 'none';
        panelVide.style.display = '';
        lignes.forEach(function(l) { l.classList.remove('emp-ligne-active'); });
    });
})();
</script>
