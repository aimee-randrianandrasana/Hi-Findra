<h1>Connexion</h1>
<p class="sous-titre">Saisissez vos identifiants pour acceder au registre</p>

<?php if (!empty($erreurs['general'])): ?>
    <div class="alerte-generale"><?= e($erreurs['general']) ?></div>
<?php endif; ?>

<?php if ($message = flash('succes')): ?>
    <div class="alerte-succes"><?= e($message) ?></div>
<?php endif; ?>

<form method="post" action="<?= e(url('connexion')) ?>" novalidate>
    <?= csrf_field() ?>

    <label for="email">Adresse email</label>
    <input type="email" id="email" name="email" value="<?= e($ancienEmail) ?>" required autofocus>

    <label for="mot_de_passe">Mot de passe</label>
    <input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="8">

    <div class="case-souvenir">
        <input type="checkbox" id="se_souvenir" name="se_souvenir" value="1">
        <label for="se_souvenir" style="margin: 0; font-weight: 400">Se souvenir de moi</label>
    </div>

    <button type="submit">Se connecter</button>
</form>

<p class="lien-secondaire">
    <a href="<?= e(url('mot-de-passe-oublie')) ?>">Mot de passe oublie ?</a>
</p>
<p class="lien-secondaire">
    Pas encore de compte ? <a href="<?= e(url('inscription')) ?>">Inscrivez-vous</a>
</p>
