<h2>Changer mon mot de passe</h2>

<form method="post" action="<?= e(url('profil/mot-de-passe')) ?>" style="max-width: 400px" novalidate>
    <?= csrf_field() ?>

    <div class="champ">
        <label for="ancien_mot_de_passe">Mot de passe actuel</label>
        <input type="password" id="ancien_mot_de_passe" name="ancien_mot_de_passe" required>
        <button type="button" class="btn-afficher-mdp" aria-label="Afficher le mot de passe">
            <svg class="icon-oeil" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="icon-oeil-barre" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
        </button>
        <?php if ($msg = $erreurs['ancien_mot_de_passe'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
    </div>

    <div class="champ">
        <label for="nouveau_mot_de_passe">Nouveau mot de passe</label>
        <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" required minlength="8">
        <button type="button" class="btn-afficher-mdp" aria-label="Afficher le mot de passe">
            <svg class="icon-oeil" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="icon-oeil-barre" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
        </button>
        <?php if ($msg = $erreurs['nouveau_mot_de_passe'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
    </div>

    <div class="champ">
        <label for="confirmation_mdp">Confirmer le nouveau mot de passe</label>
        <input type="password" id="confirmation_mdp" name="confirmation_mdp" required minlength="8">
        <button type="button" class="btn-afficher-mdp" aria-label="Afficher le mot de passe">
            <svg class="icon-oeil" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="icon-oeil-barre" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
        </button>
        <?php if ($msg = $erreurs['confirmation_mdp'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
    </div>

    <div class="btn-group">
        <button type="submit" class="btn btn-primaire">Mettre a jour</button>
        <a href="<?= e(url('profil')) ?>" class="btn btn-secondaire">Annuler</a>
    </div>
</form>
