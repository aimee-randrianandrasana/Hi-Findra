<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e(app_name()) ?></title>
    <link rel="stylesheet" href="<?= e(url('css/app.css')) ?>">
</head>
<body>
<?php
$u   = auth_user();
$uri = $_SERVER['REQUEST_URI'];
function actif(string $segment, string $uri): string {
    return str_contains($uri, $segment) ? 'actif' : '';
}
?>

<nav class="navbar" id="navbar">
    <div class="navbar-gauche">
        <button class="navbar-burger" id="navbar-burger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
        <a href="<?= e(url('')) ?>" class="navbar-logo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            <span>Mi-Findra</span>
        </a>
    </div>

    <div class="navbar-liens" id="navbar-liens">
        <a href="<?= e(url('')) ?>" class="navbar-lien <?= ($uri === '/' || $uri === '/accueil' || str_starts_with($uri, '/accueil')) ? 'actif' : '' ?>">Accueil</a>
        <a href="<?= e(url('employes')) ?>" class="navbar-lien <?= actif('/employes', $uri) ?>">Employes</a>
        <a href="<?= e(url('lieux')) ?>" class="navbar-lien <?= actif('/lieux', $uri) ?>">Lieux</a>
        <a href="<?= e(url('affectations')) ?>" class="navbar-lien <?= actif('/affectations', $uri) ?>">Affectations</a>
        <?php if (has_role('administrateur')): ?>
            <a href="<?= e(url('utilisateurs')) ?>" class="navbar-lien <?= actif('/utilisateurs', $uri) ?>">Utilisateurs</a>
        <?php endif; ?>
    </div>

    <div class="navbar-droite">
        <button type="button" class="navbar-theme" id="theme-toggle" title="Changer de theme" aria-label="Changer de theme">
            <svg class="icon-lune" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
            <svg class="icon-soleil" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
        </button>
        <a href="<?= e(url('profil')) ?>" class="navbar-profil">
            <?php if (!empty($u['photo'])): ?>
                <img src="<?= e(url('uploads/' . $u['photo'])) ?>" alt="" class="navbar-avatar">
            <?php else: ?>
                <div class="navbar-avatar"><?= e(mb_strtoupper(mb_substr($u['prenom'] ?? '?', 0, 1) . mb_substr($u['nom'] ?? '', 0, 1))) ?></div>
            <?php endif; ?>
            <!-- <span class="navbar-nom"><?= e(($u['nom'] ?? '') . ' ' . ($u['prenom'] ?? '')) ?></span> -->
        </a>
        <button type="button" class="navbar-deco" title="Deconnexion" data-confirme="modale-deconnexion">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        </button>
    </div>
</nav>

<div class="fond-modale" id="modale-deconnexion">
    <div class="modale">
        <h3>Confirmer la deconnexion</h3>
        <p>Voulez-vous vraiment vous deconnecter ?</p>
        <div class="actions-modale">
            <button type="button" class="btn btn-secondaire" data-fermer-modale>Annuler</button>
            <form method="post" action="<?= e(url('deconnexion')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger">Se deconnecter</button>
            </form>
        </div>
    </div>
</div>

<main class="contenu">
    <div class="page">
        <?php if ($msg = flash('succes')): ?><div class="alerte alerte-succes"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('erreur')): ?><div class="alerte alerte-erreur"><?= e($msg) ?></div><?php endif; ?>
        <?= $content ?>
    </div>
</main>

<script src="<?= e(url('js/app.js')) ?>"></script>
</body>
</html>
