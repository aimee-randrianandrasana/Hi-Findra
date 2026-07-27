<h2>Mot de passe oublie</h2>

<?php if ($message): ?>
    <div class="flash-success"><?= e($message) ?></div>
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
