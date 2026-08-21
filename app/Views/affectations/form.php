<div class="page-modale">
    <div class="page-modale-fond" data-page-modale-close></div>
    <div class="page-modale-contenu">
        <div class="carte carte-form">
            <h2 style="margin-top:0;margin-bottom:1.2rem">Nouvelle affectation</h2>
            <form method="post" action="<?= e(url('affectations')) ?>" novalidate>
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
                    <label for="num_emp">Employe</label>
                    <select id="num_emp" name="num_emp" required>
                        <option value="">-- Selectionnez d'abord un lieu --</option>
                        <?php foreach ($employes as $employe): ?>
                            <option value="<?= $employe['num_emp'] ?>"
                                data-lieu-id="<?= $employe['id_lieu'] ?>"
                                <?= (int) ($anciennes['num_emp'] ?? 0) === (int) $employe['num_emp'] ? 'selected' : '' ?>>
                                <?= e($employe['civilite'] . ' ' . $employe['nom'] . ' ' . $employe['prenom']) ?> — <?= e($employe['lieu_designation']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($msg = $erreurs['num_emp'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                    <small class="champ-note">Seuls les employes n'etant pas encore a ce lieu sont affiches.</small>
                </div>

                <div class="champ">
                    <label for="numero_arrete">Numero d'arrete</label>
                    <input type="text" id="numero_arrete" name="numero_arrete" value="<?= e($anciennes['numero_arrete'] ?? $prochainNumero) ?>" required>
                    <?php if ($msg = $erreurs['numero_arrete'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
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
                    <label for="notifier_email" style="margin: 0">Notifier l'employe par email immediatement</label>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primaire">Enregistrer l'affectation</button>
                    <a href="<?= e(url('affectations')) ?>" class="btn btn-secondaire">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    var lieuSelect = document.getElementById('nouveau_lieu_id');
    var empSelect = document.getElementById('num_emp');
    var options = Array.from(empSelect.options).filter(function(o) { return o.value !== ''; });
    var placeholder = empSelect.querySelector('option[value=""]');

    var employePreselected = <?= json_encode((int) ($anciennes['num_emp'] ?? 0)) ?>;

    function filtrer() {
        var lieuId = lieuSelect.value;
        var selectedVal = empSelect.value;

        empSelect.innerHTML = '';
        empSelect.appendChild(placeholder);

        if (lieuId === '') {
            placeholder.textContent = '-- Selectionnez d\'abord un lieu --';
            options.forEach(function(o) { o.style.display = ''; empSelect.appendChild(o); });
            return;
        }

        placeholder.textContent = '-- Choisir un employe --';
        var visible = 0;
        options.forEach(function(o) {
            if (o.dataset.lieuId === lieuId) {
                o.style.display = 'none';
            } else {
                o.style.display = '';
                visible++;
                empSelect.appendChild(o);
            }
        });

        if (visible === 0) {
            placeholder.textContent = '-- Tous les employes sont deja a ce lieu --';
        }

        if (selectedVal && empSelect.querySelector('option[value="' + selectedVal + '"]')) {
            empSelect.value = selectedVal;
        }

        if (employePreselected && empSelect.querySelector('option[value="' + employePreselected + '"]')) {
            empSelect.value = String(employePreselected);
        }
    }

    lieuSelect.addEventListener('change', filtrer);
    if (lieuSelect.value) { filtrer(); }

    if (employePreselected) {
        var preEmp = options.find(function(o) { return o.value === String(employePreselected); });
        if (preEmp) {
            placeholder.textContent = '-- Choisissez un lieu different de l\'actuel --';
            options.forEach(function(o) { o.style.display = ''; empSelect.appendChild(o); });
            empSelect.value = String(employePreselected);
        }
    }
})();
</script>
