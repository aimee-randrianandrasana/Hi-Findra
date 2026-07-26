<div class="entete-page">
    <div>
        <h1>Affectations</h1>
        <p><?= (int) $total ?> affectation(s) enregistree(s)</p>
    </div>
    <a href="<?= e(url('affectations/creer')) ?>" class="btn btn-primaire">+ Nouvelle affectation</a>
</div>

<div class="carte emp-layout">
    <div class="barre-outils">
        <form method="get" action="<?= e(url('affectations')) ?>" style="display: flex; gap: .5rem; flex-wrap: wrap">
            <input type="date" name="debut" value="<?= e($debut) ?>" title="Date de debut">
            <input type="date" name="fin" value="<?= e($fin) ?>" title="Date de fin">
            <button type="submit" class="btn btn-secondaire">Filtrer par periode</button>
            <?php if ($debut || $fin): ?>
                <a href="<?= e(url('affectations')) ?>" class="btn btn-secondaire">Reinitialiser</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($affectations)): ?>
        <p class="vide">Aucune affectation trouvee.</p>
    <?php else: ?>
        <div class="emp-container">
            <div class="emp-table-wrapper">
                <table class="tableau" id="tableau-affectations">
                    <thead>
                        <tr>
                            <th data-triable>N° Arrete</th>
                            <th>Employe</th>
                            <th data-triable>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($affectations as $a): ?>
                            <tr class="emp-ligne"
                                data-num="<?= $a['num_affect'] ?>"
                                data-arrete="<?= e($a['numero_arrete']) ?>"
                                data-employe="<?= e($a['employe_nom'] . ' ' . $a['employe_prenom']) ?>"
                                data-photo="<?= e(!empty($a['employe_photo']) ? url('uploads/' . $a['employe_photo']) : gravatar_url($a['employe_mail'], 60)) ?>"
                                data-ancien-lieu="<?= e($a['ancien_lieu_designation'] ?? '—') ?>"
                                data-nouveau-lieu="<?= e($a['nouveau_lieu_designation']) ?>"
                                data-date-affect="<?= e(date('d/m/Y', strtotime($a['date_affect']))) ?>"
                                data-date-prise="<?= e(date('d/m/Y', strtotime($a['date_prise_service']))) ?>"
                                data-notifie="<?= $a['notifie_par_mail'] ? 'oui' : 'non' ?>"
                                data-url-pdf="<?= e(url('affectations/' . $a['num_affect'] . '/pdf')) ?>"
                                data-url-edit="<?= e(url('affectations/' . $a['num_affect'] . '/editer')) ?>"
                                data-url-notifier="<?= e(url('affectations/' . $a['num_affect'] . '/notifier')) ?>">
                                <td><span class="cachet">N° <?= e($a['numero_arrete']) ?></span></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: .5rem">
                                        <div class="avatar-employe" style="cursor: pointer; width: 28px; height: 28px; font-size: .6rem">
                                            <img src="<?= e(!empty($a['employe_photo']) ? url('uploads/' . $a['employe_photo']) : gravatar_url($a['employe_mail'], 60)) ?>" alt="">
                                        </div>
                                        <span><?= e($a['employe_nom'] . ' ' . $a['employe_prenom']) ?></span>
                                    </div>
                                </td>
                                <td><?= e(date('d/m/Y', strtotime($a['date_affect']))) ?></td>
                                <td class="cellule-actions">
                                    <?php if (!$a['notifie_par_mail']): ?>
                                        <form method="post" action="<?= e(url('affectations/' . $a['num_affect'] . '/notifier')) ?>" style="display:inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-secondaire btn-sm">Notifier</button>
                                        </form>
                                    <?php endif; ?>
                                    <button class="btn btn-danger btn-sm" data-confirme="modale-suppr-<?= $a['num_affect'] ?>">Supprimer</button>
                                </td>
                            </tr>

                            <div class="fond-modale" id="modale-suppr-<?= $a['num_affect'] ?>">
                                <div class="modale">
                                    <h3>Confirmer la suppression</h3>
                                    <p>Supprimer l'affectation N°<strong><?= e($a['numero_arrete']) ?></strong> ?</p>
                                    <div class="actions-modale">
                                        <button type="button" class="btn btn-secondaire" data-fermer-modale>Annuler</button>
                                        <form method="post" action="<?= e(url('affectations/' . $a['num_affect'] . '/supprimer')) ?>">
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

            <aside class="emp-panel" id="aff-panel">
                <div class="emp-panel-vide" id="aff-panel-vide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32"><path d="M5 12h14M14 6l6 6-6 6"/></svg>
                    <p>Cliquez sur une affectation<br>pour voir sa fiche</p>
                </div>
                <div class="emp-panel-content" id="aff-panel-content" style="display: none">
                    <button type="button" class="emp-panel-fermer" id="aff-panel-fermer" title="Fermer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <div class="emp-panel-entete">
                        <div class="emp-panel-avatar" style="width:48px;height:48px">
                            <img src="" alt="" id="aff-panel-photo">
                        </div>
                        <div>
                            <h3 id="aff-panel-employe"></h3>
                            <p class="emp-panel-poste" id="aff-panel-arrete"></p>
                        </div>
                    </div>
                    <dl class="emp-panel-details">
                        <dt>Ancien lieu</dt>
                        <dd id="aff-panel-ancien"></dd>
                        <dt>Nouveau lieu</dt>
                        <dd><span class="badge badge-vert" id="aff-panel-nouveau"></span></dd>
                        <dt>Date arrete</dt>
                        <dd id="aff-panel-date-affect"></dd>
                        <dt>Prise de service</dt>
                        <dd id="aff-panel-date-prise"></dd>
                        <dt>Notifie</dt>
                        <dd id="aff-panel-notifie"></dd>
                    </dl>
                    <div class="emp-panel-actions" style="flex-wrap:wrap">
                        <a href="" class="btn btn-primaire btn-sm" id="aff-panel-pdf" target="_blank">PDF</a>
                        <a href="" class="btn btn-secondaire btn-sm" id="aff-panel-edit">Modifier</a>
                    </div>
                </div>
            </aside>
        </div>

        <?php
        $base = 'affectations';
        $params = ($debut || $fin) ? ['debut' => $debut, 'fin' => $fin] : [];
        require dirname(__DIR__) . '/partials/pagination.php';
        ?>
    <?php endif; ?>
</div>

<script>
(function() {
    var panelContent = document.getElementById('aff-panel-content');
    var panelVide = document.getElementById('aff-panel-vide');
    var panelFermer = document.getElementById('aff-panel-fermer');

    var lignes = document.querySelectorAll('#tableau-affectations .emp-ligne');

    function remplir(l) {
        document.getElementById('aff-panel-photo').src = l.dataset.photo;
        document.getElementById('aff-panel-employe').textContent = l.dataset.employe;
        document.getElementById('aff-panel-arrete').textContent = 'N\u00b0 ' + l.dataset.arrete;
        document.getElementById('aff-panel-ancien').textContent = l.dataset.ancienLieu;
        document.getElementById('aff-panel-nouveau').textContent = l.dataset.nouveauLieu;
        document.getElementById('aff-panel-date-affect').textContent = l.dataset.dateAffect;
        document.getElementById('aff-panel-date-prise').textContent = l.dataset.datePrise;
        document.getElementById('aff-panel-notifie').textContent = l.dataset.notifie === 'oui' ? 'Oui' : 'Non';
        document.getElementById('aff-panel-pdf').href = l.dataset.urlPdf;
        document.getElementById('aff-panel-edit').href = l.dataset.urlEdit;
    }

    lignes.forEach(function(l) {
        l.addEventListener('click', function(e) {
            if (e.target.closest('.cellule-actions, .avatar-employe img')) return;
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
