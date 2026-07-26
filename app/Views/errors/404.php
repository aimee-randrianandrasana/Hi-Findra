<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>404 - Page introuvable</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .box {
            text-align: center;
        }

        h1 {
            font-size: 5rem;
            margin: 0;
            color: #3b82f6;
        }

        a {
            color: #60a5fa;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>404</h1>
        <p>La page demandee n'existe pas.</p>
        <a href="<?= e($_ENV['APP_URL'] ?? '/') ?>">Retour a l'accueil</a>
    </div>
</body>
</html>
