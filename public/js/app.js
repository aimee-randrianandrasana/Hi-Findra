document.addEventListener('DOMContentLoaded', function () {

// Theme sombre / clair
    var savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.documentElement.dataset.theme = savedTheme;
    } else {
        document.documentElement.dataset.theme = 'dark';
    }

    var themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        function updateThemeIcons() {
            var isDark = document.documentElement.dataset.theme === 'dark';
            var lune = themeToggle.querySelector('.icon-lune');
            var soleil = themeToggle.querySelector('.icon-soleil');
            if (lune) lune.style.display = isDark ? 'none' : 'block';
            if (soleil) soleil.style.display = isDark ? 'block' : 'none';
        }
        updateThemeIcons();
        themeToggle.addEventListener('click', function () {
            var isDark = document.documentElement.dataset.theme === 'dark';
            document.documentElement.dataset.theme = isDark ? 'light' : 'dark';
            localStorage.setItem('theme', document.documentElement.dataset.theme);
            updateThemeIcons();
        });
    }

// Navbar mobile
    var burger = document.getElementById('navbar-burger');
    var liens  = document.getElementById('navbar-liens');

    if (burger && liens) {
        burger.addEventListener('click', function () {
            liens.classList.toggle('ouvert');
        });

        // Fermer apres clic sur un lien
        liens.querySelectorAll('.navbar-lien').forEach(function (lien) {
            lien.addEventListener('click', function () {
                liens.classList.remove('ouvert');
            });
        });

        // Fermer au clic exterieur
        document.addEventListener('click', function (e) {
            if (!liens.contains(e.target) && e.target !== burger && !burger.contains(e.target)) {
                liens.classList.remove('ouvert');
            }
        });
    }

// Modales de confirmation
    document.querySelectorAll('[data-confirme]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            var modale = document.getElementById(el.dataset.confirme);
            if (modale) modale.classList.add('ouverte');
        });
    });

    document.querySelectorAll('[data-fermer-modale]').forEach(function (el) {
        el.addEventListener('click', function () {
            el.closest('.fond-modale')?.classList.remove('ouverte');
        });
    });

    document.querySelectorAll('.fond-modale').forEach(function (fond) {
        fond.addEventListener('click', function (e) {
            if (e.target === fond) fond.classList.remove('ouverte');
        });
    });

// Recherche instantanee
    document.querySelectorAll('[data-recherche-instantanee]').forEach(function (input) {
        var tableau = document.querySelector(input.dataset.rechercheInstantanee);
        if (!tableau) return;

        input.addEventListener('input', function () {
            var terme = input.value.trim().toLowerCase();
            tableau.querySelectorAll('tbody tr').forEach(function (ligne) {
                ligne.style.display = ligne.textContent.toLowerCase().includes(terme) ? '' : 'none';
            });
        });
    });

// Tri des colonnes
    document.querySelectorAll('table.tableau').forEach(function (tableau) {
        var corps = tableau.querySelector('tbody');
        if (!corps) return;

        tableau.querySelectorAll('th[data-triable]').forEach(function (th, index) {
            var asc = true;
            th.addEventListener('click', function () {
                var lignes = Array.from(corps.querySelectorAll('tr'));
                lignes.sort(function (a, b) {
                    var va = (a.children[index]?.textContent || '').trim().toLowerCase();
                    var vb = (b.children[index]?.textContent || '').trim().toLowerCase();
                    return asc ? va.localeCompare(vb) : vb.localeCompare(va);
                });
                lignes.forEach(function (l) { corps.appendChild(l); });
                asc = !asc;
            });
        });
    });

// Compteur anime (dashboard)
    var compteurs = document.querySelectorAll('[data-compteur]');
    if (compteurs.length) {
        var animerCompteurs = function () {
            compteurs.forEach(function (el) {
                var cible = parseInt(el.dataset.compteur, 10);
                var duree = 800;
                var debut = performance.now();
                var demarre = parseInt(el.textContent, 10) || 0;

                function tick(now) {
                    var ecoule = now - debut;
                    var progres = Math.min(ecoule / duree, 1);
                    var ease = 1 - Math.pow(1 - progres, 3);
                    var valeur = Math.floor(demarre + (cible - demarre) * ease);
                    el.textContent = valeur;
                    if (progres < 1) requestAnimationFrame(tick);
                }

                requestAnimationFrame(tick);
            });
        };

        if (window.IntersectionObserver) {
            var obs = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        animerCompteurs();
                        obs.disconnect();
                    }
                });
            });
            obs.observe(compteurs[0].closest('.db-bento') || compteurs[0]);
        } else {
            animerCompteurs();
        }
    }

// Lightbox
    var overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';
    var lbImg = document.createElement('img');
    overlay.appendChild(lbImg);
    document.body.appendChild(overlay);

    overlay.addEventListener('click', function () {
        overlay.classList.remove('visible');
    });

    document.addEventListener('click', function (e) {
        var target = e.target.closest('.js-lightbox');
        if (!target) return;
        e.preventDefault();
        lbImg.src = target.src;
        overlay.classList.add('visible');
    });

// Upload photo employe
    window.changerPhotoProfil = function (numEmp, input) {
        var fichier = input.files[0];
        if (!fichier) return;

        var formData = new FormData();
        formData.append('photo', fichier);
        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) formData.append('csrf_token', csrfToken.content);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/employes/' + numEmp + '/photo', true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                var reponse = JSON.parse(xhr.responseText);
                if (reponse.success) {
                    // Mettre a jour tous les avatars avec la nouvelle photo
                    var src = '/uploads/' + reponse.photo + '?t=' + Date.now();
                    document.querySelectorAll('[data-emp-avatar="' + numEmp + '"]').forEach(function (img) {
                        img.src = src;
                    });
                    document.querySelectorAll('[data-emp-initiale="' + numEmp + '"]').forEach(function (el) {
                        el.style.display = 'none';
                    });
                    document.querySelectorAll('[data-emp-img="' + numEmp + '"]').forEach(function (img) {
                        img.src = src;
                        img.style.display = '';
                    });
                    // Recharger la page pour etre propre
                    location.reload();
                } else {
                    alert(reponse.message || 'Erreur lors du telechargement.');
                }
            } else {
                alert('Erreur serveur.');
            }
        };

        xhr.send(formData);
    };

// Afficher / masquer les mots de passe
    document.querySelectorAll('.btn-afficher-mdp').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = btn.closest('.champ, .champ-flottant, .champ-flottant-mdp').querySelector('input');
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
});
