<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(app_name()) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --fond:       #ffffff;
            --carte:      #f8f8f8;
            --bordure:    #e0e0e0;
            --texte:      #2c2c2c;
            --muted:      #666666;
            --accent:     #2F6B4F;
            --accent-2:   #1F4E39;
            --btn-bg:     #e8f0eb;
            --btn-txt:    #1F4E39;
            --btn-bord:   #c0d4c6;
            --btn-hov-bg: #d4e6da;
            --err-bg:     #fce8e8;
            --err-bord:   #f0c0c0;
            --err-txt:    #b6442f;
            --suc-bg:     #e8f5ee;
            --suc-bord:   #b0dcc0;
            --suc-txt:    #2F6B4F;
            --input-bg:   #ffffff;
        }

        [data-theme="dark"] {
            --fond:       #0a1628;
            --carte:      #111d32;
            --bordure:    #1c2d44;
            --texte:      #c8d6e5;
            --muted:      #8899aa;
            --accent:     #8adca8;
            --accent-2:   #6aaa7a;
            --btn-bg:     #0e2a1e;
            --btn-txt:    #6aaa7a;
            --btn-bord:   #1a4a30;
            --btn-hov-bg: #1a4a30;
            --err-bg:     #2a1218;
            --err-bord:   #4a1a2a;
            --err-txt:    #aa6a6a;
            --suc-bg:     #122a1a;
            --suc-bord:   #1a4a30;
            --suc-txt:    #8adca8;
            --input-bg:   #0a1628;
        }

        body {
            background-color: var(--fond);
            color: var(--texte);
            font-family: 'Segoe UI', 'Noto Sans', sans-serif;
            font-size: 13px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .3s, color .3s;
        }

        .login-box {
            width: 100%;
            max-width: 360px;
            padding: 1.2rem;
        }

        .login-title {
            font-family: 'Caveat', cursive;
            font-size: 42px;
            font-weight: 700;
            font-style: italic;
            color: var(--texte);
            text-align: center;
            margin-bottom: 4px;
        }

        .login-sub {
            text-align: center;
            color: var(--accent);
            margin-bottom: 24px;
            font-size: 13px;
        }

        .login-card {
            background: var(--carte);
            border: 2px solid var(--bordure);
            border-radius: 8px;
            padding: 28px 24px;
        }

        .login-card h1,
        .login-card h2 {
            text-align: center;
            font-size: 16px;
            color: var(--muted);
            margin-bottom: 20px;
            font-weight: 600;
        }

        .champ-flottant {
            position: relative;
            margin-bottom: 14px;
        }

        .champ-flottant input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--bordure);
            color: var(--texte);
            border-radius: 6px;
            padding: 20px 12px 8px;
            font-size: 13px;
            font-family: inherit;
            transition: border-color .2s, background .3s, color .3s;
        }

        .champ-flottant label {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 13px;
            pointer-events: none;
            transition: all .35s ease;
            padding: 0 4px;
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
        }

        .champ-flottant input:focus + label,
        .champ-flottant input:not(:placeholder-shown) + label {
            top: 0;
            transform: translateY(-50%);
            font-size: 10.5px;
            font-weight: 600;
            color: var(--accent);
            background: var(--carte);
        }

        .champ-flottant input::placeholder {
            color: transparent;
        }

        .champ-flottant input:focus {
            outline: none;
            border-color: #3a5a80;
        }

        .champ-flottant-mdp input {
            padding-right: 70px;
        }

        .btn-afficher-mdp {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 2px 4px;
            color: #5a7a9a;
            font-size: 12px;
            font-family: inherit;
            font-weight: 600;
            transition: color .15s;
            box-shadow: none;
            width: auto;
        }

        .btn-afficher-mdp:hover {
            color: var(--accent);
            transform: translateY(-50%);
        }

        .erreur {
            color: var(--err-txt);
            font-size: .85rem;
            margin: -4px 0 10px;
        }

        .alerte-generale,
        .flash-error {
            background: var(--err-bg);
            border: 1px solid var(--err-bord);
            color: var(--err-txt);
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .alerte-succes,
        .flash-success {
            background: var(--suc-bg);
            border: 1px solid var(--suc-bord);
            color: var(--suc-txt);
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .case-souvenir {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            font-size: 13px;
            color: var(--muted);
        }

        .case-souvenir input[type="checkbox"] {
            width: auto;
            accent-color: var(--accent);
        }

        .case-souvenir label {
            margin: 0;
            font-weight: 400;
            text-transform: none;
            font-size: 13px;
            color: var(--muted);
            position: static;
            transform: none;
            background: none;
            padding: 0;
            pointer-events: auto;
        }

        button[type="submit"],
        .login-btn {
            width: 100%;
            background: #0e2a1e;
            color: #6aaa7a;
            border: 1px solid #1a4a30;
            border-radius: 6px;
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            font-family: inherit;
            margin-top: 8px;
            transition: all .15s;
        }

        [data-theme="light"] button[type="submit"],
        [data-theme="light"] .login-btn {
            background: #2F6B4F;
            color: #ffffff;
            border-color: #1F4E39;
        }

        button[type="submit"]:hover,
        .login-btn:hover {
            background: #1a4a30;
            color: #c8d6e5;
        }

        [data-theme="light"] button[type="submit"]:hover,
        [data-theme="light"] .login-btn:hover {
            background: #1F4E39;
            color: #ffffff;
        }

        .lien-secondaire {
            text-align: center;
            margin-top: 16px;
            font-size: 12px;
            color: #5a7a9a;
        }

        .lien-secondaire a {
            color: #6a8aba;
            text-decoration: none;
            font-weight: 600;
            transition: color .15s;
        }

        .lien-secondaire a:hover {
            text-decoration: underline;
        }

        .auth-theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--bordure);
            background: var(--carte);
            color: var(--texte);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s, color .15s;
            z-index: 10;
        }

        .auth-theme-toggle:hover {
            background: var(--accent);
            color: var(--fond);
        }

        .auth-theme-toggle svg {
            width: 18px;
            height: 18px;
        }

        .icon-lune { display: none; }
        .icon-soleil { display: block; }
        [data-theme="light"] .icon-lune { display: block; }
        [data-theme="light"] .icon-soleil { display: none; }
    </style>
</head>
<body>
    <button type="button" class="auth-theme-toggle" id="theme-toggle" title="Changer de theme">
        <svg class="icon-lune" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
        <svg class="icon-soleil" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
    </button>
    <div class="login-box">
        <div class="login-title"><?= e(app_name()) ?></div>
        <div class="login-sub">Gestion des affectations</div>
        <div class="login-card">
            <?= $content ?>
        </div>
    </div>
    <script>
    (function() {
        var saved = localStorage.getItem('theme');
        document.documentElement.dataset.theme = saved || 'dark';

        var toggle = document.getElementById('theme-toggle');
        toggle.addEventListener('click', function() {
            var current = document.documentElement.dataset.theme;
            document.documentElement.dataset.theme = current === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', document.documentElement.dataset.theme);
        });

        document.querySelectorAll('.btn-afficher-mdp').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var input = btn.closest('.champ-flottant').querySelector('input');
                if (input.type === 'password') {
                    input.type = 'text';
                    btn.textContent = 'Masquer';
                } else {
                    input.type = 'password';
                    btn.textContent = 'Afficher';
                }
                input.focus();
            });
        });
    })();
    </script>
</body>
</html>
