<h1>Mot de passe oublie</h1>
<p class="sous-titre">Recevez un lien de reinitialisation par email</p>

<?php if ($message): ?>
    <div class="alerte-succes"><?= e($message) ?></div>
<?php else: ?>
    <form method="post" action="<?= e(url('mot-de-passe-oublie')) ?>" novalidate>
        <?= csrf_field() ?>

        <div class="champ-flottant">
            <input type="email" id="email" name="email" placeholder=" " required autofocus>
            <label for="email">Adresse email</label>
        </div>

        <button type="submit">Envoyer le lien</button>
    </form>
<?php endif; ?>

<p class="lien-secondaire">
    <a href="<?= e(url('connexion')) ?>">Retour a la connexion</a>
</p>
