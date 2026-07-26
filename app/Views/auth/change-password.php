<h2>Changer mon mot de passe</h2>

<form method="post" action="<?= e(url('profil/mot-de-passe')) ?>" style="max-width: 400px" novalidate>
    <?= csrf_field() ?>

    <div class="champ">
        <label for="ancien_mot_de_passe">Mot de passe actuel</label>
        <input type="password" id="ancien_mot_de_passe" name="ancien_mot_de_passe" required>
        <?php if ($msg = $erreurs['ancien_mot_de_passe'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
    </div>

    <div class="champ">
        <label for="nouveau_mot_de_passe">Nouveau mot de passe</label>
        <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" required minlength="8">
        <?php if ($msg = $erreurs['nouveau_mot_de_passe'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
    </div>

    <div class="champ">
        <label for="confirmation_mdp">Confirmer le nouveau mot de passe</label>
        <input type="password" id="confirmation_mdp" name="confirmation_mdp" required minlength="8">
        <?php if ($msg = $erreurs['confirmation_mdp'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
    </div>

    <div class="btn-group">
        <button type="submit" class="btn btn-primaire">Mettre a jour</button>
        <a href="<?= e(url('profil')) ?>" class="btn btn-secondaire">Annuler</a>
    </div>
</form>
