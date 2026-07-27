<div class="entete-page">
    <div>
        <h1>Historique des affectations</h1>
        <p><?= (int) $total ?> affectation(s) notifiee(s)</p>
    </div>
    <div style="display: flex; gap: .6rem">
        <a href="<?= e(url('affectations')) ?>" class="btn btn-secondaire">Retour aux affectations</a>
        <?php if ($total > 0): ?>
            <a href="<?= e(url('affectations/historique/imprimer')) ?>" class="btn btn-secondaire" target="_blank">Imprimer</a>
            <button type="button" class="btn btn-danger" data-confirme="modale-vider">Vider l'historique</button>
        <?php endif; ?>
    </div>
</div>

<div class="fond-modale" id="modale-vider">
    <div class="modale">
        <h3>Confirmer le vidage</h3>
        <p>Voulez-vous vraiment supprimer toutes les affectations de l'historique ? Cette action est irreversible.</p>
        <div class="actions-modale">
            <button type="button" class="btn btn-secondaire" data-fermer-modale>Annuler</button>
            <form method="post" action="<?= e(url('affectations/historique/vider')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger">Vider</button>
            </form>
        </div>
    </div>
</div>

<div class="carte emp-layout">
    <?php if (empty($affectations)): ?>
        <p class="vide">Aucune affectation dans l'historique.</p>
    <?php else: ?>
        <div class="emp-container">
            <div class="emp-table-wrapper">
                <table class="tableau" id="tableau-historique">
                    <thead>
                        <tr>
                            <th data-triable>N° Arrete</th>
                            <th>Employe</th>
                            <th>Email</th>
                            <th>Destination</th>
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
                                data-mail="<?= e($a['employe_mail']) ?>"
                                data-photo="<?= e(!empty($a['employe_photo']) ? url('uploads/' . $a['employe_photo']) : gravatar_url($a['employe_mail'], 60)) ?>"
                                data-ancien-lieu="<?= e($a['ancien_lieu_designation'] ?? '—') ?>"
                                data-nouveau-lieu="<?= e($a['nouveau_lieu_designation']) ?>"
                                data-date-affect="<?= e(date('d/m/Y', strtotime($a['date_affect']))) ?>"
                                data-date-prise="<?= e(date('d/m/Y', strtotime($a['date_prise_service']))) ?>"
                                data-notifie="<?= $a['notifie_par_mail'] ? 'oui' : 'non' ?>">
                                <td style="font-weight:500;opacity:.75;letter-spacing:.02em">N° <?= e($a['numero_arrete']) ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: .5rem">
                                        <div class="avatar-employe" style="cursor: pointer; width: 28px; height: 28px; font-size: .6rem">
                                            <img src="<?= e(!empty($a['employe_photo']) ? url('uploads/' . $a['employe_photo']) : gravatar_url($a['employe_mail'], 60)) ?>" alt="">
                                        </div>
                                        <span><?= e($a['employe_nom'] . ' ' . $a['employe_prenom']) ?></span>
                                    </div>
                                </td>
                                <td style="color:var(--txt-2);font-size:.85em"><?= e($a['employe_mail']) ?></td>
                                <td style="color:var(--vert);font-weight:600"><?= e($a['nouveau_lieu_designation']) ?></td>
                                <td><?= e(date('d/m/Y', strtotime($a['date_affect']))) ?></td>
                                <td class="cellule-actions">
                                    <?php if (!$a['notifie_par_mail']): ?>
                                        <form method="post" action="<?= e(url('affectations/' . $a['num_affect'] . '/notifier?from=historique')) ?>" style="display:inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-secondaire btn-sm">Notifier</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
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
                        <dd id="aff-panel-nouveau" style="color:var(--vert);font-weight:600"></dd>
                        <dt>Email</dt>
                        <dd id="aff-panel-mail"></dd>
                        <dt>Date arrete</dt>
                        <dd id="aff-panel-date-affect"></dd>
                        <dt>Prise de service</dt>
                        <dd id="aff-panel-date-prise"></dd>
                        <dt>Notifie</dt>
                        <dd id="aff-panel-notifie"></dd>
                    </dl>
                </div>
            </aside>
        </div>

        <?php
        $base = 'affectations/historique';
        $params = [];
        require dirname(__DIR__) . '/partials/pagination.php';
        ?>
    <?php endif; ?>
</div>

<script>
(function() {
    var panelContent = document.getElementById('aff-panel-content');
    var panelVide = document.getElementById('aff-panel-vide');
    var panelFermer = document.getElementById('aff-panel-fermer');

    var lignes = document.querySelectorAll('#tableau-historique .emp-ligne');
    var selectedLigne = null;
    var lastHovered = null;

    function remplir(l) {
        document.getElementById('aff-panel-photo').src = l.dataset.photo;
        document.getElementById('aff-panel-employe').textContent = l.dataset.employe;
        document.getElementById('aff-panel-arrete').textContent = 'N\u00b0 ' + l.dataset.arrete;
        document.getElementById('aff-panel-ancien').textContent = l.dataset.ancienLieu;
        document.getElementById('aff-panel-nouveau').textContent = l.dataset.nouveauLieu;
        document.getElementById('aff-panel-mail').textContent = l.dataset.mail;
        document.getElementById('aff-panel-date-affect').textContent = l.dataset.dateAffect;
        document.getElementById('aff-panel-date-prise').textContent = l.dataset.datePrise;
        document.getElementById('aff-panel-notifie').textContent = l.dataset.notifie === 'oui' ? 'Oui' : 'Non';
    }

    lignes.forEach(function(l) {
        l.addEventListener('mouseenter', function() {
            lastHovered = l;
            remplir(l);
            panelVide.style.display = 'none';
            panelContent.style.display = '';
        });

        l.addEventListener('click', function(e) {
            if (e.target.closest('button, form, a[href]')) return;
            lignes.forEach(function(x) { x.classList.remove('emp-ligne-active'); });
            l.classList.add('emp-ligne-active');
            selectedLigne = l;
        });
    });

    var tableWrapper = document.querySelector('.emp-table-wrapper');
    if (tableWrapper) {
        tableWrapper.addEventListener('mouseleave', function() {
            var active = selectedLigne || lastHovered;
            if (active) remplir(active);
        });
    }

    panelFermer.addEventListener('click', function() {
        panelContent.style.display = 'none';
        panelVide.style.display = '';
        lignes.forEach(function(l) { l.classList.remove('emp-ligne-active'); });
        selectedLigne = null;
    });
})();
</script>
