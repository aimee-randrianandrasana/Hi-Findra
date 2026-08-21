<div class="entete-page">
    <div>
        <h1>Employes</h1>
        <p><?= (int) $total ?> employe(s) enregistre(s)</p>
    </div>
    <div style="display: flex; gap: .6rem; align-items: center">
        <a href="<?= e(url('employes/creer')) ?>" class="btn btn-primaire">+ Ajouter un employe</a>
    </div>
</div>

<div class="carte emp-layout">
    <div class="barre-outils">
        <div style="display: flex; gap: .5rem; flex-wrap: wrap; align-items: center">
            <input type="search" value="<?= e($terme) ?>" placeholder="Rechercher par nom ou prenom..."
                   data-recherche-instantanee="#tableau-employes" style="flex:1;min-width:160px">
            <select id="filtre-civilite" style="width:auto">
                <option value="">Toutes civilites</option>
                <option value="Mr">Mr</option>
                <option value="Mlle">Mlle</option>
                <option value="Mme">Mme</option>
            </select>
            <form method="get" action="<?= e(url('employes')) ?>" style="margin-left:auto;display:inline">
                <button type="submit" name="jamais" value="<?= $jamais ? '0' : '1' ?>" class="btn btn-secondaire btn-sm">
                    <?= $jamais ? 'Tous les employes' : 'Jamais affectes' ?>
                </button>
            </form>
        </div>
    </div>

    <?php if (empty($employes)): ?>
        <p class="vide">Aucun employe trouve.</p>
    <?php else: ?>
        <div class="emp-container">
            <div class="emp-table-wrapper">
                <table class="tableau" id="tableau-employes">
                    <thead>
                        <tr>
                            <th>Profil</th>
                            <th>Civilite</th>
                            <th data-triable>Nom</th>
                            <th data-triable>Prenom</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employes as $employe): ?>
                            <?php
                            $photoUrl = !empty($employe['photo'])
                                ? url('uploads/' . $employe['photo'])
                                : gravatar_url($employe['mail'], 60);
                            $photoFull = !empty($employe['photo'])
                                ? url('uploads/' . $employe['photo'])
                                : gravatar_url($employe['mail'], 200);
                            ?>
                            <tr class="emp-ligne" data-num="<?= $employe['num_emp'] ?>"
                                data-photo="<?= e($photoUrl) ?>"
                                data-photo-full="<?= e($photoFull) ?>"
                                data-civilite="<?= e($employe['civilite']) ?>"
                                data-nom="<?= e($employe['nom']) ?>"
                                data-prenom="<?= e($employe['prenom']) ?>"
                                data-poste="<?= e($employe['poste']) ?>"
                                data-lieu="<?= e($employe['lieu_designation']) ?>"
                                data-province="<?= e($employe['lieu_province'] ?? '') ?>"
                                data-mail="<?= e($employe['mail']) ?>"
                                data-matricule="<?= e(str_pad((string) $employe['num_emp'], 4, '0', STR_PAD_LEFT)) ?>"
                                data-url-edit="<?= e(url('employes/' . $employe['num_emp'] . '/editer')) ?>"
                                data-url-historique="<?= e(url('employes/' . $employe['num_emp'] . '/historique')) ?>"
                                data-url-affecter="<?= e(url('affectations/creer?employe=' . $employe['num_emp'])) ?>">
                                <td>
                                    <div class="avatar-employe" title="Voir la fiche">
                                        <img src="<?= $photoUrl ?>" alt="">
                                    </div>
                                </td>
                                <td><?= e($employe['civilite']) ?></td>
                                <td><?= e($employe['nom']) ?></td>
                                <td><?= e($employe['prenom']) ?></td>
                                <td class="cellule-actions">
                                    <button class="btn btn-danger btn-sm" data-confirme="modale-suppr-<?= $employe['num_emp'] ?>">Licencier</button>
                                </td>
                            </tr>

                            <!-- MODALE CONFIRMATION SUPPRESSION -->
                            <div class="fond-modale" id="modale-suppr-<?= $employe['num_emp'] ?>">
                                <div class="modale">
                                    <h3>Confirmer le licenciement</h3>
                                    <p>Licencier l'employe <strong><?= e($employe['nom'] . ' ' . $employe['prenom']) ?></strong> ? Tout son historique d'affectations sera egalement supprime.</p>
                                    <div class="actions-modale">
                                        <button type="button" class="btn btn-secondaire" data-fermer-modale>Annuler</button>
                                        <form method="post" action="<?= e(url('employes/' . $employe['num_emp'] . '/supprimer')) ?>">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger">Licencier</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <aside class="emp-panel" id="emp-panel">
                <div class="emp-panel-vide" id="emp-panel-vide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                    <p>Cliquez sur un employe<br>pour voir sa fiche</p>
                </div>
                <div class="emp-panel-content" id="emp-panel-content" style="display: none">
                    <button type="button" class="emp-panel-fermer" id="emp-panel-fermer" title="Fermer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <div class="emp-panel-entete">
                        <div class="emp-panel-avatar" id="panel-avatar">
                            <img src="" alt="" class="js-lightbox" id="panel-img">
                        </div>
                        <div>
                            <h3 id="panel-nom"></h3>
                            <p class="emp-panel-poste" id="panel-poste"></p>
                        </div>
                    </div>
                    <dl class="emp-panel-details">
                        <dt>Lieu actuel</dt>
                        <dd id="panel-lieu" style="color:var(--vert);font-weight:600"></dd>
                        <dt>Email</dt>
                        <dd id="panel-mail"></dd>
                        <dt>Matricule</dt>
                        <dd id="panel-matricule"></dd>
                    </dl>
                    <div class="emp-panel-actions">
                        <a href="" class="btn btn-primaire" id="panel-btn-edit">Modifier</a>
                        <a href="" class="btn btn-secondaire" id="panel-btn-affecter">Affecter</a>
                    </div>
                    <div class="emp-panel-photo">
                        <label class="btn-photo-change" title="Changer la photo" id="panel-photo-label">
                            <input type="file" accept="image/jpeg,image/png,image/webp" id="panel-photo-input">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        </label>
                    </div>
                </div>
            </aside>
        </div>

        <?php $base = 'employes'; $params = $terme !== '' ? ['q' => $terme] : []; require dirname(__DIR__) . '/partials/pagination.php'; ?>
    <?php endif; ?>
</div>

<script>
(function() {
    var panel = document.getElementById('emp-panel');
    var panelContent = document.getElementById('emp-panel-content');
    var panelVide = document.getElementById('emp-panel-vide');
    var panelFermer = document.getElementById('emp-panel-fermer');

    var lignes = document.querySelectorAll('.emp-ligne');
    var selectedLigne = null;
    var lastHovered = null;

    function remplirPanel(ligne) {
        document.getElementById('panel-img').src = ligne.dataset.photoFull;
        document.getElementById('panel-nom').textContent = ligne.dataset.civilite + ' ' + ligne.dataset.nom + ' ' + ligne.dataset.prenom;
        document.getElementById('panel-poste').textContent = ligne.dataset.poste;
        document.getElementById('panel-lieu').textContent = ligne.dataset.lieu + (ligne.dataset.province ? ' (' + ligne.dataset.province + ')' : '');
        document.getElementById('panel-mail').textContent = ligne.dataset.mail;
        document.getElementById('panel-matricule').textContent = '#' + ligne.dataset.matricule;
        document.getElementById('panel-btn-edit').href = ligne.dataset.urlEdit;
        document.getElementById('panel-btn-affecter').href = ligne.dataset.urlAffecter;
        var input = document.getElementById('panel-photo-input');
        var newInput = input.cloneNode(true);
        input.parentNode.replaceChild(newInput, input);
        newInput.addEventListener('change', function() {
            if (typeof changerPhotoProfil === 'function') {
                changerPhotoProfil(ligne.dataset.num, this);
            }
        });
    }

    lignes.forEach(function(ligne) {
        ligne.addEventListener('mouseenter', function(e) {
            lastHovered = ligne;
            remplirPanel(ligne);
            panelVide.style.display = 'none';
            panelContent.style.display = '';
        });

        ligne.addEventListener('click', function(e) {
            if (e.target.closest('button, form, a[href]')) return;
            lignes.forEach(function(l) { l.classList.remove('emp-ligne-active'); });
            ligne.classList.add('emp-ligne-active');
            selectedLigne = ligne;
        });
    });

    var tableWrapper = document.querySelector('.emp-table-wrapper');
    if (tableWrapper) {
        tableWrapper.addEventListener('mouseleave', function() {
            var active = selectedLigne || lastHovered;
            if (active) {
                remplirPanel(active);
            }
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

    // Filtre par civilite
    var filtreCivilite = document.getElementById('filtre-civilite');
    if (filtreCivilite) {
        function appliquerFiltre() {
            var val = filtreCivilite.value;
            lignes.forEach(function(l) {
                if (!val || l.dataset.civilite === val) {
                    l.style.display = '';
                } else {
                    l.style.display = 'none';
                }
            });
        }
        filtreCivilite.addEventListener('change', appliquerFiltre);
    }
})();
</script>
