<h1>Nouveau mot de passe</h1>
<p class="sous-titre">Choisissez un nouveau mot de passe</p>

<form method="post" action="<?= e(url('reinitialiser-mot-de-passe')) ?>" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="jeton" value="<?= e($jeton) ?>">

    <div class="champ-flottant champ-flottant-mdp">
        <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder=" " required minlength="8" autofocus>
        <label for="mot_de_passe">Nouveau mot de passe</label>
        <button type="button" class="btn-afficher-mdp" aria-label="Afficher le mot de passe">
            <svg class="icon-oeil" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="icon-oeil-barre" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
        </button>
    </div>
    <?php if ($msg = $erreurs['mot_de_passe'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <div class="champ-flottant champ-flottant-mdp">
        <input type="password" id="confirmation_mdp" name="confirmation_mdp" placeholder=" " required minlength="8">
        <label for="confirmation_mdp">Confirmer le mot de passe</label>
        <button type="button" class="btn-afficher-mdp" aria-label="Afficher le mot de passe">
            <svg class="icon-oeil" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="icon-oeil-barre" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
        </button>
    </div>
    <?php if ($msg = $erreurs['confirmation_mdp'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>

    <button type="submit">Reinitialiser le mot de passe</button>
</form>
