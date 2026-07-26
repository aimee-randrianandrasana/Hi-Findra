<div class="entete-page">
    <h1><?= $lieu ? 'Modifier le lieu' : 'Ajouter un lieu' ?></h1>
</div>

<div class="carte" style="max-width: 520px">
    <form method="post" action="<?= e($lieu ? url('lieux/' . $lieu['id_lieu']) : url('lieux')) ?>" novalidate>
        <?= csrf_field() ?>

        <div class="champ">
            <label for="province">Province</label>
            <select id="province" name="province" required>
                <option value="">-- Choisir une province --</option>
                <?php $provinces = ['Antananarivo', 'Antsiranana', 'Fianarantsoa', 'Mahajanga', 'Toamasina', 'Toliara']; ?>
                <?php foreach ($provinces as $p): ?>
                    <option value="<?= $p ?>" <?= ($anciennes['province'] ?? '') === $p ? 'selected' : '' ?>><?= $p ?></option>
                <?php endforeach; ?>
            </select>
            <?php if ($msg = $erreurs['province'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
        </div>

        <div class="champ">
            <label for="designation">Designation</label>
            <input type="text" id="designation" name="designation" value="<?= e($anciennes['designation'] ?? '') ?>" placeholder="Ex: Lycee Moderne Ampefiloha" required>
            <?php if ($msg = $erreurs['designation'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primaire"><?= $lieu ? 'Mettre a jour' : 'Enregistrer' ?></button>
            <a href="<?= e(url('lieux')) ?>" class="btn btn-secondaire">Annuler</a>
        </div>
    </form>
</div>
