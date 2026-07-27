<div class="page-modale">
    <div class="page-modale-fond" data-page-modale-close></div>
    <div class="page-modale-contenu">
        <div class="carte carte-form">
            <h2 style="margin-top:0;margin-bottom:1.2rem"><?= $employe ? 'Modifier un employe' : 'Ajouter un employe' ?></h2>
            <form method="post" action="<?= e($employe ? url('employes/' . $employe['num_emp']) : url('employes')) ?>" novalidate>
                <?= csrf_field() ?>

                <div class="grille-formulaire">
                    <div class="champ">
                        <label for="civilite">Civilite</label>
                        <select id="civilite" name="civilite" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach (['Mr', 'Mlle', 'Mme'] as $c): ?>
                                <option value="<?= $c ?>" <?= ($anciennes['civilite'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($msg = $erreurs['civilite'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                    </div>

                    <div class="champ">
                        <label for="id_lieu">Lieu actuel</label>
                        <select id="id_lieu" name="id_lieu" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($lieux as $lieu): ?>
                                <option value="<?= $lieu['id_lieu'] ?>" <?= (int) ($anciennes['id_lieu'] ?? 0) === (int) $lieu['id_lieu'] ? 'selected' : '' ?>>
                                    <?= e($lieu['designation']) ?> (<?= e($lieu['province']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($msg = $erreurs['id_lieu'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                    </div>
                </div>

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
                    <label for="mail">Email</label>
                    <input type="email" id="mail" name="mail" value="<?= e($anciennes['mail'] ?? '') ?>" required>
                    <?php if ($msg = $erreurs['mail'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                </div>

                <div class="champ">
                    <label for="poste">Poste</label>
                    <input type="text" id="poste" name="poste" value="<?= e($anciennes['poste'] ?? '') ?>" required>
                    <?php if ($msg = $erreurs['poste'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primaire"><?= $employe ? 'Mettre a jour' : 'Enregistrer' ?></button>
                    <a href="<?= e(url('employes')) ?>" class="btn btn-secondaire">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
