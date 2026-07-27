<div class="entete-page">
    <div>
        <h1>Mon profil</h1>
        <p>Gerez vos informations personnelles et votre photo</p>
    </div>
</div>

<div class="carte carte-form" style="max-width: 560px">
    <h3>Informations personnelles</h3>

    <form method="post" action="<?= e(url('profil')) ?>" enctype="multipart/form-data" novalidate>
        <?= csrf_field() ?>

        <div style="display: flex; align-items: center; gap: 1.2rem; margin-bottom: 1.3rem">
            <?php if (!empty($utilisateur['photo'])): ?>
                <img src="<?= e(url('uploads/' . $utilisateur['photo'])) ?>" alt="Photo de profil"
                     style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 1px solid var(--bord)">
            <?php else: ?>
                <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--badge-vert-bg); color: var(--badge-vert-txt); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.3rem">
                    <?= e(mb_substr($utilisateur['prenom'] ?? '?', 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div style="flex: 1">
                <label for="photo">Photo de profil</label>
                <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,.webp">
                <small class="champ-note">JPG, PNG ou WEBP — 2 Mo maximum.</small>
            </div>
        </div>

        <div class="grille-formulaire">
            <div class="champ">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="<?= e($utilisateur['nom']) ?>" required>
                <?php if ($msg = $erreurs['nom'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
            </div>
            <div class="champ">
                <label for="prenom">Prenom</label>
                <input type="text" id="prenom" name="prenom" value="<?= e($utilisateur['prenom']) ?>" required>
                <?php if ($msg = $erreurs['prenom'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
            </div>
        </div>

        <div class="champ">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($utilisateur['email']) ?>" required>
            <?php if ($msg = $erreurs['email'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
        </div>

        <div class="champ">
            <label>Role</label>
            <input type="text" value="<?= e($utilisateur['role']) ?>" disabled style="background: var(--fond); color: var(--txt-2)">
            <small class="champ-note">Le role ne peut etre modifie que par un autre administrateur.</small>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primaire">Enregistrer les modifications</button>
        </div>
    </form>
</div>

<div class="carte carte-form" style="max-width: 560px">
    <h3>Mot de passe</h3>
    <p style="color: var(--txt-2); font-size: .85rem; margin-top: -.5rem">
        Pour des raisons de securite, le changement de mot de passe se fait sur une page dediee.
    </p>
    <a href="<?= e(url('profil/mot-de-passe')) ?>" class="btn btn-secondaire">Changer mon mot de passe</a>
</div>
