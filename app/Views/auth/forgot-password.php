<h1>Mot de passe oublie</h1>
<p class="sous-titre">Recevez un lien de reinitialisation par email</p>

<?php if ($message): ?>
    <div class="alerte-succes"><?= e($message) ?></div>
<?php else: ?>
    <form method="post" action="<?= e(url('mot-de-passe-oublie')) ?>" novalidate>
        <?= csrf_field() ?>

        <label for="email">Adresse email</label>
        <input type="email" id="email" name="email" required autofocus>

        <button type="submit">Envoyer le lien</button>
    </form>
<?php endif; ?>

<p class="lien-secondaire">
    <a href="<?= e(url('connexion')) ?>">Retour a la connexion</a>
</p>
