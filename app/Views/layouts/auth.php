<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(app_name()) ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600;700&family=Inter:wght@400;500;600;700&display=swap');

        :root {
            --vert:        #2F6B4F;
            --vert-fonce:  #1F4E39;
            --vert-clair:  #E7F1EC;
            --fond:        #EBEFEC;
            --surface:     #F8FAF9;
            --bordure:     #D5DDD8;
            --texte:       #1C2420;
            --texte-att:   #6B7570;
            --rouge:       #B6442F;
            --rouge-clair: #FBEAE6;
        }

        [data-theme="dark"] {
            --vert:        #4ade80;
            --vert-fonce:  #166534;
            --vert-clair:  #14532d;
            --fond:        #0f1419;
            --surface:     #1a2332;
            --bordure:     #2d3748;
            --texte:       #e2e8f0;
            --texte-att:   #94a3b8;
            --rouge:       #f87171;
            --rouge-clair: #450a0a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--fond);
            font-family: 'Inter', Arial, sans-serif;
            padding: 1.2rem;
        }

        .fiche-conteneur {
            width: 100%;
            max-width: 380px;
        }

        .entete-fiche {
            text-align: center;
            margin-bottom: 1.4rem;
        }

        .entete-fiche h1 {
            font-family: 'Source Serif 4', serif;
            font-size: 1.3rem;
            margin: 0;
            font-weight: 700;
            color: var(--vert-fonce);
        }

        .carte {
            background: var(--surface);
            width: 100%;
            padding: 2rem 1.9rem 1.7rem;
            border-radius: 14px;
            border: 1px solid var(--bordure);
            box-shadow: 0 8px 24px rgba(31, 78, 57, .07);
        }

        .carte h1 {
            font-size: 1.25rem;
            color: var(--texte);
            margin: 0 0 .2rem;
            font-family: 'Inter';
            font-weight: 700;
        }

        .carte p.sous-titre {
            margin: 0 0 1.4rem;
            color: var(--texte-att);
            font-size: .85rem;
        }

        label {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: var(--texte);
            margin-bottom: .32rem;
        }

        input[type=text],
        input[type=email],
        input[type=password] {
            width: 100%;
            padding: .6rem .75rem;
            border: 1px solid var(--bordure);
            border-radius: 8px;
            margin-bottom: .9rem;
            font-size: .9rem;
            font-family: 'Inter';
            background: var(--surface);
            color: var(--texte);
            transition: border-color .15s;
        }

        input:focus {
            outline: none;
            border-color: var(--vert);
            box-shadow: 0 0 0 3px var(--vert-clair);
        }

        .erreur {
            color: var(--rouge);
            font-size: .78rem;
            margin: -.65rem 0 .8rem;
        }

        .alerte-generale {
            background: var(--rouge-clair);
            color: var(--rouge);
            padding: .65rem .9rem;
            border-radius: 8px;
            font-size: .85rem;
            margin-bottom: 1rem;
        }

        .alerte-succes {
            background: var(--vert-clair);
            color: var(--vert-fonce);
            padding: .65rem .9rem;
            border-radius: 8px;
            font-size: .85rem;
            margin-bottom: 1rem;
        }

        button {
            width: 100%;
            padding: .72rem;
            background: var(--vert);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: .9rem;
            font-weight: 600;
            font-family: 'Inter';
            cursor: pointer;
            transition: background .15s;
        }

        button:hover {
            background: var(--vert-fonce);
        }

        .lien-secondaire {
            text-align: center;
            margin-top: 1.2rem;
            font-size: .85rem;
            color: var(--texte-att);
        }

        .lien-secondaire a {
            color: var(--vert);
            text-decoration: none;
            font-weight: 600;
        }

        .lien-secondaire a:hover {
            text-decoration: underline;
        }

        .case-souvenir {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1.2rem;
            font-size: .85rem;
            color: var(--texte);
        }

        .case-souvenir input {
            width: auto;
            margin: 0;
        }

        .auth-theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--bordure);
            background: var(--surface);
            color: var(--texte);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s, color .15s;
            z-index: 10;
        }

        .auth-theme-toggle:hover {
            background: var(--vert-clair);
            color: var(--vert);
        }

        .auth-theme-toggle svg {
            width: 18px;
            height: 18px;
        }

        [data-theme="dark"] .auth-theme-toggle .icon-lune { display: none; }
        [data-theme="dark"] .auth-theme-toggle .icon-soleil { display: block !important; }
        :root:not([data-theme="dark"]) .auth-theme-toggle .icon-lune { display: block; }
        :root:not([data-theme="dark"]) .auth-theme-toggle .icon-soleil { display: none; }
    </style>
</head>
<body>
    <button type="button" class="auth-theme-toggle" id="theme-toggle" title="Changer de theme" aria-label="Changer de theme">
        <svg class="icon-lune" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
        <svg class="icon-soleil" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
    </button>
    <div class="fiche-conteneur">
        <div class="entete-fiche">
        </div>
        <div class="carte">
            <?= $content ?>
        </div>
    </div>
    <script>
    (function() {
        var toggle = document.getElementById('theme-toggle');
        var saved = localStorage.getItem('theme');
        if (saved) {
            document.documentElement.dataset.theme = saved;
        } else {
            document.documentElement.dataset.theme = 'dark';
        }
        function updateIcons() {
            var isDark = document.documentElement.dataset.theme === 'dark';
            var lune = toggle.querySelector('.icon-lune');
            var soleil = toggle.querySelector('.icon-soleil');
            if (lune) lune.style.display = isDark ? 'none' : 'block';
            if (soleil) soleil.style.display = isDark ? 'block' : 'none';
        }
        updateIcons();
        toggle.addEventListener('click', function() {
            var isDark = document.documentElement.dataset.theme === 'dark';
            document.documentElement.dataset.theme = isDark ? 'light' : 'dark';
            localStorage.setItem('theme', document.documentElement.dataset.theme);
            updateIcons();
        });
    })();
    </script>
</body>
</html>
