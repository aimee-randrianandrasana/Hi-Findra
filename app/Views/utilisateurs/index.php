<div class="entete-page">
    <div>
        <h1>Utilisateurs</h1>
        <p><?= (int) $total ?> compte(s)</p>
    </div>
    <a href="<?= e(url('utilisateurs/creer')) ?>" class="btn btn-primaire">+ Ajouter un utilisateur</a>
</div>

<div class="carte emp-layout">
    <div class="barre-outils">
        <form method="get" action="<?= e(url('utilisateurs')) ?>" style="display: flex; gap: .5rem">
            <input type="search" name="q" value="<?= e($terme) ?>" placeholder="Rechercher par nom, prenom ou email..."
                   data-recherche-instantanee="#tableau-utilisateurs">
            <button type="submit" class="btn btn-secondaire">Rechercher</button>
        </form>
    </div>

    <?php if (empty($utilisateurs)): ?>
        <p class="vide">Aucun utilisateur trouve.</p>
    <?php else: ?>
        <div class="emp-container">
            <div class="emp-table-wrapper">
                <table class="tableau" id="tableau-utilisateurs">
                    <thead>
                        <tr>
                            <th>Profil</th>
                            <th data-triable>Nom</th>
                            <th data-triable>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $u): ?>
                            <?php
                            $photoUrl = !empty($u['photo']) ? url('uploads/' . $u['photo']) : gravatar_url($u['email'], 60);
                            $photoFull = !empty($u['photo']) ? url('uploads/' . $u['photo']) : gravatar_url($u['email'], 200);
                            ?>
                            <tr class="emp-ligne"
                                data-id="<?= $u['id'] ?>"
                                data-photo="<?= e($photoUrl) ?>"
                                data-photo-full="<?= e($photoFull) ?>"
                                data-nom="<?= e($u['nom']) ?>"
                                data-prenom="<?= e($u['prenom']) ?>"
                                data-email="<?= e($u['email']) ?>"
                                data-role="<?= e($u['role']) ?>"
                                data-statut="<?= e($u['statut']) ?>"
                                data-url-edit="<?= e(url('utilisateurs/' . $u['id'] . '/editer')) ?>"
                                data-url-statut="<?= e(url('utilisateurs/' . $u['id'] . '/statut')) ?>">
                                <td>
                                    <div class="avatar-employe" style="width: 32px; height: 32px; font-size: .65rem; cursor: pointer">
                                        <img src="<?= $photoUrl ?>" alt="">
                                    </div>
                                </td>
                                <td><?= e($u['nom'] . ' ' . $u['prenom']) ?></td>
                                <td><?= e($u['email']) ?></td>
                                <td class="cellule-actions">
                                    <button class="btn btn-danger btn-sm" data-confirme="modale-suppr-<?= $u['id'] ?>">Supprimer</button>
                                </td>
                            </tr>

                            <div class="fond-modale" id="modale-suppr-<?= $u['id'] ?>">
                                <div class="modale">
                                    <h3>Confirmer la suppression</h3>
                                    <p>Supprimer le compte de <strong><?= e($u['nom'] . ' ' . $u['prenom']) ?></strong> ?</p>
                                    <div class="actions-modale">
                                        <button type="button" class="btn btn-secondaire" data-fermer-modale>Annuler</button>
                                        <form method="post" action="<?= e(url('utilisateurs/' . $u['id'] . '/supprimer')) ?>">
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

            <aside class="emp-panel" id="user-panel">
                <div class="emp-panel-vide" id="user-panel-vide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                    <p>Cliquez sur un utilisateur<br>pour voir sa fiche</p>
                </div>
                <div class="emp-panel-content" id="user-panel-content" style="display: none">
                    <button type="button" class="emp-panel-fermer" id="user-panel-fermer" title="Fermer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <div class="emp-panel-entete">
                        <div class="emp-panel-avatar" id="user-panel-avatar">
                            <img src="" alt="" class="js-lightbox" id="user-panel-img">
                        </div>
                        <div>
                            <h3 id="user-panel-nom"></h3>
                            <p class="emp-panel-poste" id="user-panel-role"></p>
                        </div>
                    </div>
                    <dl class="emp-panel-details">
                        <dt>Email</dt>
                        <dd id="user-panel-email"></dd>
                        <dt>Statut</dt>
                        <dd id="user-panel-statut"></dd>
                    </dl>
                    <div class="emp-panel-actions">
                        <a href="" class="btn btn-primaire" id="user-panel-edit">Modifier</a>
                        <form method="post" action="" id="user-panel-statut-form" style="display:inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-secondaire" id="user-panel-btn-statut"></button>
                        </form>
                    </div>
                </div>
            </aside>
        </div>

        <?php $base = 'utilisateurs'; $params = $terme !== '' ? ['q' => $terme] : []; require dirname(__DIR__) . '/partials/pagination.php'; ?>
    <?php endif; ?>
</div>

<script>
(function() {
    var panelContent = document.getElementById('user-panel-content');
    var panelVide = document.getElementById('user-panel-vide');
    var panelFermer = document.getElementById('user-panel-fermer');

    var lignes = document.querySelectorAll('#tableau-utilisateurs .emp-ligne');

    function remplir(l) {
        document.getElementById('user-panel-img').src = l.dataset.photoFull;
        document.getElementById('user-panel-nom').textContent = l.dataset.nom + ' ' + l.dataset.prenom;
        document.getElementById('user-panel-role').textContent = l.dataset.role;
        document.getElementById('user-panel-email').textContent = l.dataset.email;

        var statutEl = document.getElementById('user-panel-statut');
        statutEl.innerHTML = l.dataset.statut === 'actif'
            ? '<span class="badge badge-vert">Actif</span>'
            : '<span class="badge badge-rouge">Inactif</span>';

        document.getElementById('user-panel-edit').href = l.dataset.urlEdit;

        var form = document.getElementById('user-panel-statut-form');
        form.action = l.dataset.urlStatut;
        var btn = document.getElementById('user-panel-btn-statut');
        btn.textContent = l.dataset.statut === 'actif' ? 'Desactiver' : 'Activer';
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
