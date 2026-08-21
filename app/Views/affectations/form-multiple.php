<div class="page-modale">
    <div class="page-modale-fond" data-page-modale-close></div>
    <div class="page-modale-contenu">
        <div class="carte carte-form">
            <h2 style="margin-top:0;margin-bottom:1.2rem">Nouvelle affectation multiple</h2>
            <form method="post" action="<?= e(url('affectations/enregistrer-multiple')) ?>" novalidate>
                <?= csrf_field() ?>

                <div class="champ">
                    <label for="nouveau_lieu_id">Nouveau lieu d'affectation</label>
                    <select id="nouveau_lieu_id" name="nouveau_lieu_id" required>
                        <option value="">-- Choisir un lieu --</option>
                        <?php foreach ($lieux as $lieu): ?>
                            <option value="<?= $lieu['id_lieu'] ?>" <?= (int) ($anciennes['nouveau_lieu_id'] ?? 0) === (int) $lieu['id_lieu'] ? 'selected' : '' ?>>
                                <?= e($lieu['designation']) ?> (<?= e($lieu['province']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($msg = $erreurs['nouveau_lieu_id'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                </div>

                <div class="champ">
                    <label>Employes</label>
                    <div class="checkbox-liste" id="liste-employes">
                        <label class="checkbox-item" style="font-weight:600;border-bottom:1px solid var(--bord);padding-bottom:.5rem;margin-bottom:.2rem">
                            <input type="checkbox" id="tout-choisir" style="width:auto">
                            Tout selectionner
                        </label>
                        <?php $checkedIds = (array) ($anciennes['num_employes'] ?? []); ?>
                        <?php foreach ($employes as $employe): ?>
                            <label class="checkbox-item" data-lieu-id="<?= $employe['id_lieu'] ?>">
                                <input type="checkbox" name="num_employes[]" value="<?= $employe['num_emp'] ?>" <?= in_array((string) $employe['num_emp'], $checkedIds, true) ? 'checked' : '' ?> style="width:auto">
                                <?= e($employe['civilite'] . ' ' . $employe['nom'] . ' ' . $employe['prenom']) ?> — <?= e($employe['lieu_designation']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($msg = $erreurs['num_employes'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                    <small class="champ-note">Seuls les employes n'etant pas encore a ce lieu sont affiches.</small>
                </div>

                <div class="grille-formulaire">
                    <div class="champ">
                        <label for="date_affect">Date de l'arrete</label>
                        <input type="date" id="date_affect" name="date_affect" value="<?= e($anciennes['date_affect'] ?? date('Y-m-d')) ?>" min="<?= date('Y-m-d') ?>" required>
                        <?php if ($msg = $erreurs['date_affect'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                    </div>

                    <div class="champ">
                        <label for="date_prise_service">Date de prise de service</label>
                        <input type="date" id="date_prise_service" name="date_prise_service" value="<?= e($anciennes['date_prise_service'] ?? '') ?>" required>
                        <?php if ($msg = $erreurs['date_prise_service'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="champ">
                    <label for="raison">Raison de l'affectation</label>
                    <input type="text" id="raison" name="raison" value="<?= e($anciennes['raison'] ?? '') ?>" placeholder="Ex : Promotion, Demande, Mauvaise conduite...">
                    <?php if ($msg = $erreurs['raison'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                </div>

                <div class="champ" style="display: flex; align-items: center; gap: .5rem">
                    <input type="checkbox" id="notifier_email" name="notifier_email" value="1" style="width: auto">
                    <label for="notifier_email" style="margin: 0">Notifier les employes par email immediatement</label>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primaire">Enregistrer les affectations</button>
                    <a href="<?= e(url('affectations')) ?>" class="btn btn-secondaire">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    var lieuSelect = document.getElementById('nouveau_lieu_id');
    var liste = document.getElementById('liste-employes');
    var tout = document.getElementById('tout-choisir');
    var labels = Array.from(liste.querySelectorAll('.checkbox-item[data-lieu-id]'));

    function filtrer() {
        var lieuId = lieuSelect.value;
        var visible = 0;

        labels.forEach(function(lbl) {
            if (lieuId === '' || lbl.dataset.lieuId !== lieuId) {
                lbl.style.display = '';
                visible++;
            } else {
                lbl.style.display = 'none';
                var cb = lbl.querySelector('input[type="checkbox"]');
                if (cb) { cb.checked = false; }
            }
        });

        if (tout) {
            tout.parentElement.style.display = lieuId === '' ? '' : '';
            tout.checked = false;
        }
    }

    lieuSelect.addEventListener('change', filtrer);
    if (lieuSelect.value) { filtrer(); }

    if (tout) {
        tout.addEventListener('change', function() {
            labels.forEach(function(lbl) {
                if (lbl.style.display !== 'none') {
                    var cb = lbl.querySelector('input[type="checkbox"]');
                    if (cb) { cb.checked = tout.checked; }
                }
            });
        });
    }
})();
</script>
