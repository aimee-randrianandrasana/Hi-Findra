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

    <div class="champ-flottant">
        <input type="email" id="email" name="email" value="<?= e($ancienEmail) ?>" placeholder=" " required autofocus>
        <label for="email">Adresse email</label>
    </div>

    <div class="champ-flottant champ-flottant-mdp">
        <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder=" " required minlength="8">
        <label for="mot_de_passe">Mot de passe</label>
        <button type="button" class="btn-afficher-mdp" aria-label="Afficher le mot de passe">
            <svg class="icon-oeil" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="icon-oeil-barre" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
        </button>
    </div>

    <div class="case-souvenir">
        <input type="checkbox" id="se_souvenir" name="se_souvenir" value="1">
        <label for="se_souvenir" style="margin: 0; font-weight: 400; text-transform: none; font-size: .85rem">Se souvenir de moi</label>
    </div>

    <button type="submit">Se connecter</button>
</form>

<p class="lien-secondaire">
    <a href="<?= e(url('mot-de-passe-oublie')) ?>">Mot de passe oublie ?</a>
</p>
<p class="lien-secondaire">
    Pas encore de compte ? <a href="<?= e(url('inscription')) ?>">Inscrivez-vous</a>
</p>
