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
            --fond:       #d8ded9;
            --carte:      #e8ece9;
            --surface-hi: #f0f4f1;
            --bordure:    #bcc5be;
            --bordure-hi: #cdd5cf;
            --texte:      #1A231E;
            --muted:      #5A6B63;
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
            --input-bg:   #d8ded9;
            --shadow-sm:  0 1px 2px rgba(0,0,0,.06), 0 1px 3px rgba(0,0,0,.10);
            --shadow-md:  0 2px 4px rgba(0,0,0,.06), 0 4px 8px rgba(0,0,0,.10);
            --shadow-lg:  0 4px 8px rgba(0,0,0,.06), 0 8px 20px rgba(0,0,0,.12);
            --shadow-xl:  0 8px 24px rgba(0,0,0,.08), 0 20px 40px rgba(0,0,0,.14);
            --shadow-inset: inset 0 2px 4px rgba(0,0,0,.08), inset 0 1px 2px rgba(0,0,0,.06);
        }

        [data-theme="dark"] {
            --fond:       #0c1015;
            --carte:      #141c28;
            --surface-hi: #1a2638;
            --bordure:    #253040;
            --bordure-hi: #303e50;
            --texte:      #e2e8f0;
            --muted:      #94a3b8;
            --accent:     #4ade80;
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
            --input-bg:   #0c1015;
            --shadow-sm:  0 1px 3px rgba(0,0,0,.25), 0 1px 2px rgba(0,0,0,.35);
            --shadow-md:  0 2px 4px rgba(0,0,0,.25), 0 4px 10px rgba(0,0,0,.35);
            --shadow-lg:  0 4px 8px rgba(0,0,0,.25), 0 8px 24px rgba(0,0,0,.35);
            --shadow-xl:  0 8px 16px rgba(0,0,0,.30), 0 20px 48px rgba(0,0,0,.40);
            --shadow-inset: inset 0 2px 4px rgba(0,0,0,.35), inset 0 1px 2px rgba(0,0,0,.30);
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
            background-image:
                radial-gradient(ellipse at 30% 30%, rgba(47,107,79,.06) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 70%, rgba(31,78,57,.04) 0%, transparent 50%);
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
            text-shadow: 0 2px 0 rgba(255,255,255,.3);
        }

        [data-theme="dark"] .login-title {
            text-shadow: 0 2px 4px rgba(0,0,0,.3);
        }

        .login-sub {
            text-align: center;
            color: var(--accent);
            margin-bottom: 24px;
            font-size: 13px;
            font-weight: 500;
            text-shadow: 0 1px 0 rgba(255,255,255,.2);
        }

        [data-theme="dark"] .login-sub {
            text-shadow: none;
        }

        .login-card {
            background: linear-gradient(165deg, var(--surface-hi) 0%, var(--carte) 40%);
            border: 1.5px solid var(--bordure);
            border-top-color: var(--bordure-hi);
            border-left-color: var(--bordure-hi);
            border-radius: 14px;
            padding: 28px 24px;
            box-shadow: var(--shadow-xl), inset 0 1px 0 rgba(255,255,255,.3);
            position: relative;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 16px;
            right: 16px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.35), transparent);
            border-radius: 14px 14px 0 0;
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
            border: 1.5px solid var(--bordure);
            border-top-color: var(--bordure-hi);
            border-left-color: var(--bordure-hi);
            color: var(--texte);
            border-radius: 10px;
            padding: 20px 12px 8px;
            font-size: 13px;
            font-family: inherit;
            transition: border-color .2s, background .3s, color .3s, box-shadow .2s;
            box-shadow: var(--shadow-inset);
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
            background: linear-gradient(165deg, var(--surface-hi) 0%, var(--carte) 40%);
        }

        .champ-flottant input::placeholder {
            color: transparent;
        }

        .champ-flottant input:focus {
            outline: none;
            border-color: var(--accent);
            border-top-color: var(--bordure);
            border-left-color: var(--bordure);
            box-shadow: var(--shadow-inset), 0 0 0 3px rgba(47, 107, 79, .15);
            background: var(--surface-hi);
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
            background: linear-gradient(180deg, #fff5f4, var(--err-bg));
            border: 1px solid var(--err-bord);
            color: var(--err-txt);
            padding: 8px 12px;
            border-radius: 10px;
            margin-bottom: 14px;
            font-size: 13px;
            box-shadow: var(--shadow-sm), inset 0 1px 0 rgba(255,255,255,.3);
        }

        .alerte-succes,
        .flash-success {
            background: linear-gradient(180deg, #edf7f0, var(--suc-bg));
            border: 1px solid var(--suc-bord);
            color: var(--suc-txt);
            padding: 8px 12px;
            border-radius: 10px;
            margin-bottom: 14px;
            font-size: 13px;
            box-shadow: var(--shadow-sm), inset 0 1px 0 rgba(255,255,255,.3);
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
            background: linear-gradient(180deg, #1a4a30 0%, #0e2a1e 100%);
            color: #6aaa7a;
            border: 1.5px solid #1a4a30;
            border-radius: 10px;
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            font-family: inherit;
            margin-top: 8px;
            transition: all .2s;
            box-shadow: 0 3px 6px rgba(14,42,30,.3), 0 6px 12px rgba(14,42,30,.2), inset 0 1px 0 rgba(255,255,255,.08);
            text-shadow: 0 1px 2px rgba(0,0,0,.2);
        }

        [data-theme="light"] button[type="submit"],
        [data-theme="light"] .login-btn {
            background: linear-gradient(180deg, #3a8c60 0%, #2F6B4F 100%);
            color: #ffffff;
            border-color: #1F4E39;
            box-shadow: 0 3px 8px rgba(47,107,79,.3), 0 6px 16px rgba(47,107,79,.15), inset 0 1px 0 rgba(255,255,255,.12);
            text-shadow: 0 1px 2px rgba(0,0,0,.15);
        }

        button[type="submit"]:hover,
        .login-btn:hover {
            background: linear-gradient(180deg, #224d3a 0%, #143828 100%);
            color: #8adca8;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(14,42,30,.35), 0 8px 20px rgba(14,42,30,.2), inset 0 1px 0 rgba(255,255,255,.1);
        }

        [data-theme="light"] button[type="submit"]:hover,
        [data-theme="light"] .login-btn:hover {
            background: linear-gradient(180deg, #4a9c70 0%, #1F4E39 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(47,107,79,.35), 0 8px 20px rgba(47,107,79,.15), inset 0 1px 0 rgba(255,255,255,.15);
        }

        button[type="submit"]:active,
        .login-btn:active {
            transform: translateY(0);
            box-shadow: var(--shadow-inset);
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
            border: 1.5px solid var(--bordure);
            background: linear-gradient(145deg, var(--surface-hi), var(--carte));
            color: var(--texte);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .2s;
            z-index: 10;
            box-shadow: var(--shadow-md), inset 0 1px 0 rgba(255,255,255,.3);
        }

        .auth-theme-toggle:hover {
            background: linear-gradient(145deg, var(--accent) 0%, var(--accent-2) 100%);
            color: #fff;
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 4px 12px rgba(47,107,79,.3), inset 0 1px 0 rgba(255,255,255,.1);
        }

        .auth-theme-toggle svg {
            width: 18px;
            height: 18px;
        }

        .credentials-hint {
            text-align: center;
            margin-top: 14px;
            font-size: 12px;
            color: var(--muted);
            text-shadow: 0 1px 0 rgba(255,255,255,.2);
        }

        [data-theme="dark"] .credentials-hint {
            text-shadow: none;
        }

        .credentials-hint strong {
            color: var(--accent);
            font-weight: 600;
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
        <div class="credentials-hint">
            Login : <strong>joker@gmail.com</strong> &mdash; Mot de passe : <strong>joker@test</strong>
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
