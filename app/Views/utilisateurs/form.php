<div class="page-modale">
    <div class="page-modale-fond" data-page-modale-close></div>
    <div class="page-modale-contenu">
        <div class="carte carte-form">
            <h2 style="margin-top:0;margin-bottom:1.2rem"><?= $utilisateur ? "Modifier l'utilisateur" : 'Ajouter un utilisateur' ?></h2>
            <form method="post" action="<?= e($utilisateur ? url('utilisateurs/' . $utilisateur['id']) : url('utilisateurs')) ?>" novalidate>
                <?= csrf_field() ?>

                <div class="grille-formulaire">
                    <div class="champ">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" value="<?= e($anciennes['nom'] ?? '') ?>" required>
                        <?php if ($msg = $erreurs['nom'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                    </div>
                    <div class="champ">
                        <label for="prenom">Prenom</label>
                        <input type="text" id="prenom" name="prenom" value="<?= e($anciennes['prenom'] ?? '') ?>" required>
                        <?php if ($msg = $erreurs['prenom'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="champ">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= e($anciennes['email'] ?? '') ?>" required>
                    <?php if ($msg = $erreurs['email'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                </div>

                <div class="champ">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <?php foreach (['developpeur' => 'Developpeur', 'administrateur' => 'Administrateur'] as $valeur => $libelle): ?>
                            <option value="<?= $valeur ?>" <?= ($anciennes['role'] ?? 'administrateur') === $valeur ? 'selected' : '' ?>><?= $libelle ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if (!$utilisateur): ?>
                    <div class="champ">
                        <label for="mot_de_passe">Mot de passe</label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="8">
                        <?php if ($msg = $erreurs['mot_de_passe'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primaire"><?= $utilisateur ? 'Mettre a jour' : 'Creer le compte' ?></button>
                    <a href="<?= e(url('utilisateurs')) ?>" class="btn btn-secondaire">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
