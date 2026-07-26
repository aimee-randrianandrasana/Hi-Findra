<style>
    .accueil-section {
        position: fixed;
        inset: 0;
        top: var(--nav-h);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .accueil-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url('<?= e(url('uploads/chaise.jpg')) ?>') center/cover no-repeat;
        z-index: 0;
    }

    .accueil-etoiles {
        position: absolute;
        inset: 0;
        z-index: 1;
        overflow: hidden;
        pointer-events: none;
    }

    .accueil-etoile {
        position: absolute;
        width: 4px; height: 4px;
        background: #56fd77;
        border-radius: 50%;
        opacity: 0;
        animation: etincelle 4s ease-in-out infinite;
        box-shadow: 0 0 6px rgba(255,255,255,.6);
    }

    @keyframes etincelle {
        0%, 100% { opacity: 0; transform: scale(0); }
        30% { opacity: .8; transform: scale(1); }
        70% { opacity: .3; transform: scale(.6); }
    }

    .accueil-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,.35) 0%, rgba(0,0,0,.55) 100%);
        z-index: 1;
    }

    .accueil-contenu {
        position: relative;
        z-index: 2;
        text-align: center;
        max-width: 560px;
        padding: 2rem;
        margin-left: 55%;
    }

    .accueil-orbite {
        position: relative;
        width: 72px;
        height: 72px;
        margin: 0 auto 1.4rem;
    }

    .accueil-orbite-cercle {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 2px solid transparent;
    }

    .accueil-orbite-cercle:nth-child(1) {
        border-top-color: #6dd5a0;
        border-right-color: rgba(109,213,160,.3);
        animation: spin 3s linear infinite;
    }

    .accueil-orbite-cercle:nth-child(2) {
        inset: 10px;
        border-bottom-color: #3d7a5e;
        border-left-color: rgba(61,122,94,.3);
        animation: spin 2s linear infinite reverse;
    }

    .accueil-orbite-cercle:nth-child(3) {
        inset: 20px;
        border-top-color: #6dd5a0;
        border-left-color: rgba(109,213,160,.3);
        animation: spin 1.8s linear infinite;
    }

    .accueil-orbite-dot {
        position: absolute;
        width: 6px; height: 6px;
        background: #6dd5a0;
        border-radius: 50%;
        top: -3px; left: 50%;
        margin-left: -3px;
        box-shadow: 0 0 10px rgba(109,213,160,.6);
    }

    .accueil-orbite-cercle:nth-child(2) .accueil-orbite-dot {
        background: #3d7a5e;
        box-shadow: 0 0 10px rgba(61,122,94,.6);
        top: auto; left: auto;
        bottom: -3px; right: -3px;
    }

    .accueil-orbite-cercle:nth-child(3) .accueil-orbite-dot {
        background: #fff;
        box-shadow: 0 0 10px rgba(255,255,255,.5);
        top: 50%; left: auto;
        right: -3px; margin-top: -3px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .accueil-contenu h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #fff;
        margin: 0 0 .3rem;
    }

    .accueil-contenu h1 span {
        background: linear-gradient(135deg, #6dd5a0, #3d7a5e);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .accueil-sous-titre {
        font-size: .9rem;
        color: rgba(255,255,255,.55);
        margin-bottom: 2rem;
        font-weight: 400;
    }

    .accueil-phrase {
        font-size: 1.05rem;
        color: rgba(255,255,255,.85);
        min-height: 2.6em;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        line-height: 1.6;
    }

    .accueil-phrase .curseur {
        display: inline-block;
        width: 2px;
        height: 1.1em;
        background: #6dd5a0;
        margin-left: 3px;
        animation: clignote .8s step-end infinite;
        vertical-align: text-bottom;
    }

    @keyframes clignote {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }

    .accueil-barre {
        width: 40px;
        height: 3px;
        background: linear-gradient(90deg, rgba(255,255,255,.2), #6dd5a0);
        border-radius: 2px;
        margin: 1.2rem auto;
    }

    .accueil-stats {
        display: flex;
        gap: 2rem;
        justify-content: center;
        margin-top: 1.5rem;
    }

    .accueil-stat {
        text-align: center;
    }

    .accueil-stat-valeur {
        font-size: 1.3rem;
        font-weight: 700;
        color: #6dd5a0;
    }

    .accueil-stat-label {
        font-size: .72rem;
        color: rgba(255,255,255,.5);
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-top: .1rem;
    }

    @media (max-width: 600px) {
        .accueil-contenu {
            margin-left: auto;
            margin-right: auto;
        }
        .accueil-contenu h1 { font-size: 1.4rem; }
        .accueil-phrase { font-size: .9rem; }
        .accueil-stats { gap: 1.2rem; }
    }
</style>

<div class="accueil-section">
    <div class="accueil-overlay"></div>
    <div class="accueil-etoiles" id="etoiles"></div>
    <div class="accueil-contenu">
        <div class="accueil-orbite">
            <div class="accueil-orbite-cercle"><span class="accueil-orbite-dot"></span></div>
            <div class="accueil-orbite-cercle"><span class="accueil-orbite-dot"></span></div>
            <div class="accueil-orbite-cercle"><span class="accueil-orbite-dot"></span></div>
        </div>
        <h1>Bienvenue sur <span>Mi-Findra</span></h1>
        <p class="accueil-sous-titre">Gestion des affectations</p>
        <div class="accueil-barre"></div>
        <div class="accueil-phrase" id="phrase"></div>
        <div class="accueil-stats">
            <div class="accueil-stat">
                <div class="accueil-stat-valeur"><?= (int)$nbEmployes ?></div>
                <div class="accueil-stat-label">Employes</div>
            </div>
            <div class="accueil-stat">
                <div class="accueil-stat-valeur"><?= (int)$nbLieux ?></div>
                <div class="accueil-stat-label">Lieux</div>
            </div>
            <div class="accueil-stat">
                <div class="accueil-stat-valeur"><?= (int)$nbAffectations ?></div>
                <div class="accueil-stat-label">Affectations</div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var phrases = [
        'Suivez les affectations de vos equipes en temps reel.',
        'Creez des arretes personnalises en un clic.',
        'Gardez tout l\'historique des mouvements.',
        'Simplifiez la gestion des mutations.'
    ];
    var el = document.getElementById('phrase');
    var i = 0, j = 0, sens = 1;

    function animer() {
        var mot = phrases[i];
        if (sens === 1) {
            el.innerHTML = mot.substring(0, j + 1) + '<span class="curseur"></span>';
            j++;
            if (j === mot.length) { sens = -1; setTimeout(animer, 2500); return; }
        } else {
            el.textContent = mot.substring(0, j);
            j--;
            if (j < 0) { j = 0; sens = 1; i = (i + 1) % phrases.length; setTimeout(animer, 400); return; }
        }
        setTimeout(animer, sens === 1 ? 35 + Math.random() * 40 : 14 + Math.random() * 20);
    }
    animer();

    // Etoiles scintillantes
    var c = document.getElementById('etoiles');
    for (var k = 0; k < 20; k++) {
        var e = document.createElement('div');
        e.className = 'accueil-etoile';
        e.style.left = Math.random() * 100 + '%';
        e.style.top = Math.random() * 100 + '%';
        e.style.animationDelay = Math.random() * 5 + 's';
        e.style.animationDuration = (3 + Math.random() * 3) + 's';
        e.style.width = e.style.height = (3 + Math.random() * 3) + 'px';
        c.appendChild(e);
    }
})();
</script>
