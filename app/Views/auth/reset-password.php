<h1>Nouveau mot de passe</h1>
<p class="sous-titre">Choisissez un nouveau mot de passe</p>

<form method="post" action="<?= e(url('reinitialiser-mot-de-passe')) ?>" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="jeton" value="<?= e($jeton) ?>">

    <label for="mot_de_passe">Nouveau mot de passe</label>
    <input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="8" autofocus>
    <?php if ($msg = $erreurs['mot_de_passe'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <label for="confirmation_mdp">Confirmer le mot de passe</label>
    <input type="password" id="confirmation_mdp" name="confirmation_mdp" required minlength="8">
    <?php if ($msg = $erreurs['confirmation_mdp'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <button type="submit">Reinitialiser le mot de passe</button>
</form>
