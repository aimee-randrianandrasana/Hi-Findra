<div class="entete-page">
    <h1>Nouvelle affectation</h1>
</div>

<div class="carte" style="max-width: 640px">
    <form method="post" action="<?= e(url('affectations')) ?>" novalidate>
        <?= csrf_field() ?>

        <div class="champ">
            <label for="numero_arrete">Numero d'arrete</label>
            <input type="text" id="numero_arrete" name="numero_arrete" value="<?= e($anciennes['numero_arrete'] ?? $prochainNumero) ?>" required
                   style="background: var(--surface)">
            <small class="champ-note">Genere automatiquement — modifiable si necessaire.</small>
            <?php if ($msg = $erreurs['numero_arrete'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
        </div>

        <div class="champ">
            <label for="num_emp">Employe</label>
            <select id="num_emp" name="num_emp" required>
                <option value="">-- Choisir un employe --</option>
                <?php foreach ($employes as $employe): ?>
                    <option value="<?= $employe['num_emp'] ?>" <?= (int) ($anciennes['num_emp'] ?? 0) === (int) $employe['num_emp'] ? 'selected' : '' ?>>
                        <?= e($employe['civilite'] . ' ' . $employe['nom'] . ' ' . $employe['prenom']) ?> — actuellement a <?= e($employe['lieu_designation']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($msg = $erreurs['num_emp'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
        </div>

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
            <small class="champ-note">L'ancien lieu sera automatiquement deduit du lieu actuel de l'employe.</small>
        </div>

        <div class="grille-formulaire">
            <div class="champ">
                <label for="date_affect">Date de l'arrete</label>
                <input type="date" id="date_affect" name="date_affect" value="<?= e($anciennes['date_affect'] ?? '') ?>" required>
                <?php if ($msg = $erreurs['date_affect'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
            </div>

            <div class="champ">
                <label for="date_prise_service">Date de prise de service</label>
                <input type="date" id="date_prise_service" name="date_prise_service" value="<?= e($anciennes['date_prise_service'] ?? '') ?>" required>
                <?php if ($msg = $erreurs['date_prise_service'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
            </div>
        </div>

        <div class="champ" style="display: flex; align-items: center; gap: .5rem">
            <input type="checkbox" id="notifier_email" name="notifier_email" value="1" style="width: auto">
            <label for="notifier_email" style="margin: 0">Notifier l'employe par email immediatement</label>
        </div>

        <button type="submit" class="btn btn-primaire">Enregistrer l'affectation</button>
        <a href="<?= e(url('affectations')) ?>" class="btn btn-secondaire">Annuler</a>
    </form>
</div>
