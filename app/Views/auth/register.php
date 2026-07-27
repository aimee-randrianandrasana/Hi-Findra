<h2>Creer un compte</h2>

<form method="post" action="<?= e(url('inscription')) ?>" novalidate>
    <?= csrf_field() ?>

    <div class="champ-flottant">
        <input type="text" id="nom" name="nom" value="<?= e($anciennes['nom'] ?? '') ?>" placeholder=" " required>
        <label for="nom">Nom</label>
    </div>
    <?php if ($msg = $erreurs['nom'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <div class="champ-flottant">
        <input type="text" id="prenom" name="prenom" value="<?= e($anciennes['prenom'] ?? '') ?>" placeholder=" " required>
        <label for="prenom">Prenom</label>
    </div>
    <?php if ($msg = $erreurs['prenom'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <div class="champ-flottant">
        <input type="email" id="email" name="email" value="<?= e($anciennes['email'] ?? '') ?>" placeholder=" " required>
        <label for="email">Adresse email</label>
    </div>
    <?php if ($msg = $erreurs['email'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <div class="champ-flottant champ-flottant-mdp">
        <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder=" " required minlength="8">
        <label for="mot_de_passe">Mot de passe</label>
        <button type="button" class="btn-afficher-mdp">Afficher</button>
    </div>
    <?php if ($msg = $erreurs['mot_de_passe'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <div class="champ-flottant champ-flottant-mdp">
        <input type="password" id="confirmation_mdp" name="confirmation_mdp" placeholder=" " required minlength="8">
        <label for="confirmation_mdp">Confirmer le mot de passe</label>
        <button type="button" class="btn-afficher-mdp">Afficher</button>
    </div>
    <?php if ($msg = $erreurs['confirmation_mdp'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <button type="submit">S'inscrire</button>
</form>

<p class="lien-secondaire">
    Deja un compte ? <a href="<?= e(url('connexion')) ?>">Connectez-vous</a>
</p>
