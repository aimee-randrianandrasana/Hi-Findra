<h2>Nouveau mot de passe</h2>

<form method="post" action="<?= e(url('reinitialiser-mot-de-passe')) ?>" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="jeton" value="<?= e($jeton) ?>">

    <div class="champ-flottant champ-flottant-mdp">
        <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder=" " required minlength="8" autofocus>
        <label for="mot_de_passe">Nouveau mot de passe</label>
        <button type="button" class="btn-afficher-mdp">Afficher</button>
    </div>
    <?php if ($msg = $erreurs['mot_de_passe'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <div class="champ-flottant champ-flottant-mdp">
        <input type="password" id="confirmation_mdp" name="confirmation_mdp" placeholder=" " required minlength="8">
        <label for="confirmation_mdp">Confirmer le mot de passe</label>
        <button type="button" class="btn-afficher-mdp">Afficher</button>
    </div>
    <?php if ($msg = $erreurs['confirmation_mdp'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <button type="submit">Reinitialiser le mot de passe</button>
</form>
