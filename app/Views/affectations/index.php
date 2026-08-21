<div class="onglets" style="display:flex;gap:0;margin-bottom:1.2rem;border-bottom:2px solid var(--bord)">
    <a href="<?= e(url('affectations?tab=toutes')) ?>" style="padding:.6rem 1.2rem;border-bottom:2px solid <?= $tab === 'toutes' ? 'var(--bleu)' : 'transparent' ?>;color:<?= $tab === 'toutes' ? 'var(--bleu)' : 'var(--txt-2)' ?>;font-weight:<?= $tab === 'toutes' ? '600' : '400' ?>;text-decoration:none;margin-bottom:-2px;transition:all .15s">Toutes (<?= $total ?>)</a>
    <a href="<?= e(url('affectations?tab=non-notifie')) ?>" style="padding:.6rem 1.2rem;border-bottom:2px solid <?= $tab === 'non-notifie' ? 'var(--bleu)' : 'transparent' ?>;color:<?= $tab === 'non-notifie' ? 'var(--bleu)' : 'var(--txt-2)' ?>;font-weight:<?= $tab === 'non-notifie' ? '600' : '400' ?>;text-decoration:none;margin-bottom:-2px;transition:all .15s">Non notifies (<?= $totalNonNotifie ?>)</a>
    <a href="<?= e(url('affectations?tab=notifie')) ?>" style="padding:.6rem 1.2rem;border-bottom:2px solid <?= $tab === 'notifie' ? 'var(--bleu)' : 'transparent' ?>;color:<?= $tab === 'notifie' ? 'var(--bleu)' : 'var(--txt-2)' ?>;font-weight:<?= $tab === 'notifie' ? '600' : '400' ?>;text-decoration:none;margin-bottom:-2px;transition:all .15s">Notifies (<?= $totalSupprime ?>)</a>
</div>

<div class="entete-page">
    <div>
        <h1>Historiques des affectations</h1>
        <p><?= (int) $total ?> affectation(s)</p>
    </div>
    <div style="display: flex; gap: .6rem">
        <?php if ($totalSupprime > 0): ?>
            <a href="<?= e(url('affectations/historique/imprimer')) ?>" class="btn btn-secondaire" target="_blank">Imprimer</a>
            <button type="button" class="btn btn-danger" data-confirme="modale-vider">Vider l'historique</button>
        <?php endif; ?>
        <div style="position: relative">
            <button class="btn btn-primaire" id="btn-nouvelle-aff" style="display: flex; align-items: center; gap: .4rem">
                + Nouvelle affectation
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="dropdown-menu" id="dropdown-aff" style="display:none; position: absolute; top: 100%; right: 0; margin-top: 4px; background: var(--surface); border-radius: 10px; box-shadow: var(--ombre); min-width: 220px; z-index: 100; overflow: hidden">
                <a href="<?= e(url('affectations/creer')) ?>" style="display: block; padding: .7rem 1rem; color: var(--txt); text-decoration: none; transition: background .15s">Affectation simple</a>
                <a href="<?= e(url('affectations/creer-multiple')) ?>" style="display: block; padding: .7rem 1rem; color: var(--txt); text-decoration: none; border-top: 1px solid var(--bord); transition: background .15s">Affectation multiple</a>
            </div>
        </div>
    </div>
</div>

<div class="fond-modale" id="modale-vider">
    <div class="modale">
        <h3>Confirmer le vidage</h3>
        <p>Voulez-vous vraiment supprimer toutes les affectations notifiees ? Cette action est irreversible.</p>
        <p style="margin-top:.8rem;color:var(--txt-2);font-size:.9em">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="vertical-align:middle;margin-right:.3rem"><path d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Pensez a <a href="<?= e(url('affectations/historique/imprimer')) ?>" target="_blank" style="text-decoration:underline">telecharger la sauvegarde PDF</a> avant de vider.
        </p>
        <div class="actions-modale">
            <button type="button" class="btn btn-secondaire" data-fermer-modale>Annuler</button>
            <form method="post" action="<?= e(url('affectations/historique/vider')) ?>" onsubmit="return confirm('Confirmez-vous avoir sauvegarde les donnees ? Cette action est irreversible.');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger">Vider</button>
            </form>
        </div>
    </div>
</div>

<div class="carte emp-layout">
    <div class="barre-outils" style="display: flex; justify-content: space-between; align-items: center; gap: .5rem; flex-wrap: wrap">
        <form method="get" action="<?= e(url('affectations')) ?>" style="display: flex; gap: .5rem; flex-wrap: wrap">
            <?php if ($tab !== 'toutes'): ?><input type="hidden" name="tab" value="<?= e($tab) ?>"><?php endif; ?>
            <input type="date" name="debut" value="<?= e($debut) ?>" title="Date de debut">
            <input type="date" name="fin" value="<?= e($fin) ?>" title="Date de fin">
            <button type="submit" class="btn btn-secondaire">Filtrer par periode</button>
            <?php if ($debut || $fin): ?>
                <a href="<?= e(url('affectations?tab=' . $tab)) ?>" class="btn btn-secondaire">Reinitialiser</a>
            <?php endif; ?>
        </form>
        <input type="text" id="recherche-aff" placeholder="Rechercher..." style="padding: .4rem .7rem; border: 1px solid var(--bord); border-radius: 8px; background: var(--surface); color: var(--txt); font-size: .85rem; outline: none; width: 200px">
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
                            <th>Email</th>
                            <th>Destination</th>
                            <th data-triable>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($affectations as $a): ?>
                            <?php $notifie = (bool) $a['supprime']; ?>
                            <tr class="emp-ligne<?= $notifie ? ' emp-ligne-notifiee' : '' ?>"
                                data-num="<?= $a['num_affect'] ?>"
                                data-arrete="<?= e($a['numero_arrete']) ?>"
                                data-employe="<?= e($a['employe_nom'] . ' ' . $a['employe_prenom']) ?>"
                                data-mail="<?= e($a['employe_mail']) ?>"
                                data-photo="<?= e(!empty($a['employe_photo']) ? url('uploads/' . $a['employe_photo']) : gravatar_url($a['employe_mail'], 60)) ?>"
                                data-ancien-lieu="<?= e($a['ancien_lieu_designation'] ?? '—') ?>"
                                data-nouveau-lieu="<?= e($a['nouveau_lieu_designation']) ?>"
                                data-date-affect="<?= e(date('d/m/Y', strtotime($a['date_affect']))) ?>"
                                data-date-prise="<?= e(date('d/m/Y', strtotime($a['date_prise_service']))) ?>"
                                data-url-pdf="<?= e(url('affectations/' . $a['num_affect'] . '/pdf')) ?>"
                                data-url-edit="<?= e(url('affectations/' . $a['num_affect'] . '/editer')) ?>"
                                data-notifie="<?= $notifie ? 'oui' : 'non' ?>">
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
                                    <?php if (!$notifie): ?>
                                        <?php if (!$a['notifie_par_mail']): ?>
                                            <form method="post" action="<?= e(url('affectations/' . $a['num_affect'] . '/notifier')) ?>" style="display:inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-secondaire btn-sm">Notifier</button>
                                            </form>
                                        <?php endif; ?>
                                        <button class="btn btn-danger btn-sm" data-confirme="modale-suppr-<?= $a['num_affect'] ?>">Supprimer</button>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <?php if (!$notifie): ?>
                            <div class="fond-modale" id="modale-suppr-<?= $a['num_affect'] ?>">
                                <div class="modale">
                                    <h3>Confirmer la suppression</h3>
                                    <p>Supprimer l'affectation N°<strong><?= e($a['numero_arrete']) ?></strong> ? Elle sera deplacee dans l'historique.</p>
                                    <div class="actions-modale">
                                        <button type="button" class="btn btn-secondaire" data-fermer-modale>Annuler</button>
                                        <form method="post" action="<?= e(url('affectations/' . $a['num_affect'] . '/supprimer')) ?>">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
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
                    <div class="emp-panel-actions" id="aff-panel-actions" style="flex-wrap:wrap">
                        <a href="" class="btn btn-primaire btn-sm" id="aff-panel-pdf" target="_blank">PDF</a>
                        <a href="" class="btn btn-secondaire btn-sm" id="aff-panel-edit">Modifier</a>
                    </div>
                </div>
            </aside>
        </div>

        <?php
        $base = 'affectations';
        $params = ['tab' => $tab];
        if ($debut || $fin) {
            $params['debut'] = $debut;
            $params['fin'] = $fin;
        }
        require dirname(__DIR__) . '/partials/pagination.php';
        ?>
    <?php endif; ?>
</div>

<script>
(function() {
    var panelContent = document.getElementById('aff-panel-content');
    var panelVide = document.getElementById('aff-panel-vide');
    var panelFermer = document.getElementById('aff-panel-fermer');
    var panelActions = document.getElementById('aff-panel-actions');

    var lignes = document.querySelectorAll('#tableau-affectations .emp-ligne');
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
        document.getElementById('aff-panel-pdf').href = l.dataset.urlPdf;
        document.getElementById('aff-panel-edit').href = l.dataset.urlEdit;
        panelActions.style.display = l.dataset.notifie === 'oui' ? 'none' : '';
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

    if (panelFermer) {
        panelFermer.addEventListener('click', function() {
            panelContent.style.display = 'none';
            panelVide.style.display = '';
            lignes.forEach(function(l) { l.classList.remove('emp-ligne-active'); });
            selectedLigne = null;
        });
    }

    var btnAff = document.getElementById('btn-nouvelle-aff');
    var dropdownAff = document.getElementById('dropdown-aff');
    if (btnAff && dropdownAff) {
        btnAff.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownAff.style.display = dropdownAff.style.display === 'none' ? 'block' : 'none';
        });
        document.addEventListener('click', function() {
            dropdownAff.style.display = 'none';
        });
        dropdownAff.querySelectorAll('a').forEach(function(a) {
            a.addEventListener('mouseenter', function() { a.style.background = 'var(--surface-hover)'; });
            a.addEventListener('mouseleave', function() { a.style.background = ''; });
        });
    }

    var rech = document.getElementById('recherche-aff');
    if (rech) {
        rech.addEventListener('input', function() {
            var q = this.value.toLowerCase().trim();
            document.querySelectorAll('#tableau-affectations .emp-ligne').forEach(function(l) {
                var txt = (l.dataset.employe + ' ' + l.dataset.mail + ' ' + l.dataset.arrete).toLowerCase();
                l.style.display = txt.includes(q) ? '' : 'none';
            });
        });
    }
})();
</script>
