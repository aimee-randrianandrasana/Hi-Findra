<div class="entete-page">
    <h1>Modifier l'affectation N°<?= e($affectation['numero_arrete']) ?></h1>
</div>

<div class="carte" style="max-width: 560px">
    <p style="color: var(--txt-2); font-size: .85rem">
        Employe : <strong><?= e($affectation['employe_nom'] . ' ' . $affectation['employe_prenom']) ?></strong> —
        <?= e($affectation['ancien_lieu_designation'] ?? '—') ?> → <?= e($affectation['nouveau_lieu_designation']) ?>
        <br>Seuls le numero d'arrete et les dates peuvent etre corriges ; l'historique des lieux reste figé.
    </p>

    <form method="post" action="<?= e(url('affectations/' . $affectation['num_affect'])) ?>" novalidate>
        <?= csrf_field() ?>

        <div class="champ">
            <label for="numero_arrete">Numero d'arrete</label>
            <input type="text" id="numero_arrete" name="numero_arrete" value="<?= e($affectation['numero_arrete']) ?>" required>
            <?php if ($msg = $erreurs['numero_arrete'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
        </div>

        <div class="grille-formulaire">
            <div class="champ">
                <label for="date_affect">Date de l'arrete</label>
                <input type="date" id="date_affect" name="date_affect" value="<?= e($affectation['date_affect']) ?>" required>
                <?php if ($msg = $erreurs['date_affect'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
            </div>

            <div class="champ">
                <label for="date_prise_service">Prise de service</label>
                <input type="date" id="date_prise_service" name="date_prise_service" value="<?= e($affectation['date_prise_service']) ?>" required>
                <?php if ($msg = $erreurs['date_prise_service'] ?? null): ?><div class="erreur"><?= e($msg) ?></div><?php endif; ?>
            </div>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primaire">Mettre a jour</button>
            <a href="<?= e(url('affectations')) ?>" class="btn btn-secondaire">Annuler</a>
        </div>
    </form>
</div>
