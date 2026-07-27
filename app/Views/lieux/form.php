<div class="page-modale">
    <div class="page-modale-fond" data-page-modale-close></div>
    <div class="page-modale-contenu">
        <div class="carte carte-form">
            <h2 style="margin-top:0;margin-bottom:1.2rem"><?= $lieu ? 'Modifier le lieu' : 'Ajouter un lieu' ?></h2>
            <form method="post" action="<?= e($lieu ? url('lieux/' . $lieu['id_lieu']) : url('lieux')) ?>" novalidate>
                <?= csrf_field() ?>

                <div class="champ">
                    <label for="province">Province</label>
                    <select id="province" name="province" required>
                        <option value="">-- Choisir une province --</option>
                        <?php $provinces = ['Analamanga', 'Matsiatra Ambony', 'Atsimo-Andrefana', 'Boeny', 'Atsinanana', 'Antsiranana']; ?>
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
    </div>
</div>
