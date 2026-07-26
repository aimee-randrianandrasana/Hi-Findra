<h1>Creer un compte</h1>


<form method="post" action="<?= e(url('inscription')) ?>" novalidate>
    <?= csrf_field() ?>

    <label for="nom">Nom</label>
    <input type="text" id="nom" name="nom" value="<?= e($anciennes['nom'] ?? '') ?>" required>
    <?php if ($msg = $erreurs['nom'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <label for="prenom">Prenom</label>
    <input type="text" id="prenom" name="prenom" value="<?= e($anciennes['prenom'] ?? '') ?>" required>
    <?php if ($msg = $erreurs['prenom'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <label for="email">Adresse email</label>
    <input type="email" id="email" name="email" value="<?= e($anciennes['email'] ?? '') ?>" required>
    <?php if ($msg = $erreurs['email'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <label for="mot_de_passe">Mot de passe</label>
    <input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="8">
    <?php if ($msg = $erreurs['mot_de_passe'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <label for="confirmation_mdp">Confirmer le mot de passe</label>
    <input type="password" id="confirmation_mdp" name="confirmation_mdp" required minlength="8">
    <?php if ($msg = $erreurs['confirmation_mdp'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <button type="submit">S'inscrire</button>
</form>

<p class="lien-secondaire">
    Deja un compte ? <a href="<?= e(url('connexion')) ?>">Connectez-vous</a>
</p>
