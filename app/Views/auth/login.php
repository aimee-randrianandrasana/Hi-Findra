<h2>Connexion</h2>

<?php if (!empty($erreurs['general'])): ?>
    <div class="flash-error"><?= e($erreurs['general']) ?></div>
<?php endif; ?>

<?php if ($message = flash('succes')): ?>
    <div class="flash-success"><?= e($message) ?></div>
<?php endif; ?>

<form method="post" action="<?= e(url('connexion')) ?>" novalidate>
    <?= csrf_field() ?>

    <div class="champ-flottant">
        <input type="email" id="email" name="email" value="<?= e($ancienEmail) ?>" placeholder=" " required autofocus>
        <label for="email">Adresse email</label>
    </div>

    <div class="champ-flottant champ-flottant-mdp">
        <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder=" " required minlength="8">
        <label for="mot_de_passe">Mot de passe</label>
        <button type="button" class="btn-afficher-mdp">Afficher</button>
    </div>

    <div class="case-souvenir">
        <input type="checkbox" id="se_souvenir" name="se_souvenir" value="1">
        <label for="se_souvenir">Se souvenir de moi</label>
    </div>

    <button type="submit">Se connecter</button>
</form>

<p class="lien-secondaire">
    <a href="<?= e(url('mot-de-passe-oublie')) ?>">Mot de passe oublie ?</a>
</p>
<p class="lien-secondaire">
    Pas encore de compte ? <a href="<?= e(url('inscription')) ?>">Inscrivez-vous</a>
</p>
<div style="text-align:center;margin-top:16px;color:#5a7a9a;font-size:12px">
    Login : <strong style="color:#6a8aba">admin@findra.mg</strong> &mdash; Mot de passe : <strong style="color:#6a8aba">admin123</strong>
</div>
