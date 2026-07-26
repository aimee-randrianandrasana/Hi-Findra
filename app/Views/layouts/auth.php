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
            padding: 2.2rem 2rem 1.8rem;
            border-radius: 16px;
            border: 1px solid var(--bordure);
            box-shadow: 0 8px 32px rgba(0, 0, 0, .08);
        }

        [data-theme="dark"] .carte {
            box-shadow: 0 8px 32px rgba(0, 0, 0, .3);
        }

        .carte h1 {
            font-size: 1.3rem;
            color: var(--texte);
            margin: 0 0 .2rem;
            font-family: 'Inter';
            font-weight: 700;
        }

        .carte p.sous-titre {
            margin: 0 0 1.6rem;
            color: var(--texte-att);
            font-size: .85rem;
        }

        label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--texte-att);
            margin-bottom: .4rem;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .champ-flottant {
            position: relative;
            margin-bottom: 1.2rem;
        }

        .champ-flottant input {
            width: 100%;
            padding: 1.1rem .9rem .45rem;
            border: 1.5px solid var(--bordure);
            border-radius: 10px;
            font-size: .9rem;
            font-family: 'Inter';
            background: var(--fond);
            color: var(--texte);
            transition: border-color .2s, box-shadow .2s, background .2s;
            margin-bottom: 0;
        }

        .champ-flottant label {
            position: absolute;
            top: 50%;
            left: .9rem;
            transform: translateY(-50%);
            font-size: .9rem;
            font-weight: 400;
            color: var(--texte-att);
            text-transform: none;
            letter-spacing: 0;
            margin-bottom: 0;
            pointer-events: none;
            transition: all .35s ease;
            background: transparent;
            padding: 0 .2rem;
        }

        .champ-flottant input:focus + label,
        .champ-flottant input:not(:placeholder-shown) + label,
        .champ-flottant input.has-value + label {
            top: 0;
            transform: translateY(-50%);
            font-size: .7rem;
            font-weight: 600;
            color: var(--vert);
            text-transform: uppercase;
            letter-spacing: .03em;
            background: var(--fond);
        }

        .champ-flottant input:focus + label {
            background: var(--surface);
        }

        .champ-flottant input::placeholder {
            color: transparent;
        }

        .champ-flottant input:hover {
            border-color: #b0b8b3;
        }

        .champ-flottant input:focus {
            outline: none;
            border-color: var(--vert);
            box-shadow: 0 0 0 3px rgba(47, 107, 79, .15);
            background: var(--surface);
        }

        [data-theme="dark"] .champ-flottant input:focus {
            box-shadow: 0 0 0 3px rgba(74, 222, 128, .1);
        }

        .champ-flottant-mdp {
            position: relative;
        }

        .champ-flottant-mdp input {
            padding-right: 2.8rem;
        }

        .btn-afficher-mdp {
            position: absolute;
            right: .6rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--texte-att);
            cursor: pointer;
            padding: .3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: color .15s, background .15s;
            width: auto;
            padding: .3rem;
            box-shadow: none;
            font-size: 0;
        }

        .btn-afficher-mdp:hover {
            color: var(--vert);
            background: var(--vert-clair);
            transform: translateY(-50%);
        }

        .btn-afficher-mdp:active {
            transform: translateY(-50%);
        }

        .btn-afficher-mdp svg {
            width: 18px;
            height: 18px;
        }

        .erreur {
            color: var(--rouge);
            font-size: .78rem;
            margin: -.7rem 0 .9rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        .erreur::before {
            content: '!';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--rouge);
            color: #fff;
            font-size: .65rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .alerte-generale {
            background: var(--rouge-clair);
            color: var(--rouge);
            padding: .7rem 1rem;
            border-radius: 10px;
            font-size: .85rem;
            margin-bottom: 1.2rem;
            border: 1px solid rgba(182, 68, 47, .2);
        }

        .alerte-succes {
            background: var(--vert-clair);
            color: var(--vert-fonce);
            padding: .7rem 1rem;
            border-radius: 10px;
            font-size: .85rem;
            margin-bottom: 1.2rem;
            border: 1px solid rgba(47, 107, 79, .2);
        }

        button {
            width: 100%;
            padding: .8rem;
            background: var(--vert);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .92rem;
            font-weight: 600;
            font-family: 'Inter';
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 2px 8px rgba(47, 107, 79, .2);
        }

        [data-theme="dark"] button {
            background: #2a3a30;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .3);
            color: #b0c4b8;
        }

        button:hover {
            background: var(--vert-fonce);
            box-shadow: 0 4px 12px rgba(47, 107, 79, .3);
            transform: translateY(-1px);
        }

        [data-theme="dark"] button:hover {
            background: #344a3c;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .4);
            color: #d0e0d6;
        }

        button:active {
            transform: translateY(0);
            box-shadow: 0 1px 4px rgba(47, 107, 79, .2);
        }

        .lien-secondaire {
            text-align: center;
            margin-top: 1.4rem;
            font-size: .85rem;
            color: var(--texte-att);
        }

        .lien-secondaire a {
            color: var(--vert);
            text-decoration: none;
            font-weight: 600;
            transition: color .15s;
        }

        .lien-secondaire a:hover {
            text-decoration: underline;
        }

        .case-souvenir {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1.4rem;
            font-size: .85rem;
            color: var(--texte);
        }

        .case-souvenir input {
            width: auto;
            margin: 0;
            accent-color: var(--vert);
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

        document.querySelectorAll('.btn-afficher-mdp').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var input = btn.closest('.champ-flottant').querySelector('input');
                var iconOeil = btn.querySelector('.icon-oeil');
                var iconOeilBarre = btn.querySelector('.icon-oeil-barre');
                if (input.type === 'password') {
                    input.type = 'text';
                    if (iconOeil) iconOeil.style.display = 'none';
                    if (iconOeilBarre) iconOeilBarre.style.display = 'block';
                } else {
                    input.type = 'password';
                    if (iconOeil) iconOeil.style.display = 'block';
                    if (iconOeilBarre) iconOeilBarre.style.display = 'none';
                }
                input.focus();
            });
        });
    })();
    </script>
</body>
</html>
