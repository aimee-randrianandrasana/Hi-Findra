document.addEventListener('DOMContentLoaded', function () {

// Theme sombre / clair
    var savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.documentElement.dataset.theme = savedTheme;
    } else {
        document.documentElement.dataset.theme = 'dark';
    }

// Indicateur navbar anime
    var indicator = document.getElementById('navbar-indicator');
    var navLienActif = document.querySelector('.navbar-lien.actif');
    var navLiens = document.querySelectorAll('.navbar-lien');

    function deplacerIndicator(el) {
        if (!indicator || !el) return;
        var liens = document.querySelector('.navbar-liens');
        var lr = liens.getBoundingClientRect();
        var er = el.getBoundingClientRect();
        indicator.style.left = (er.left - lr.left + er.width / 2 - 14) + 'px';
    }

    function initIndicator() {
        if (navLienActif) {
            deplacerIndicator(navLienActif);
        }
    }

    initIndicator();
    requestAnimationFrame(initIndicator);

    navLiens.forEach(function (lien) {
        lien.addEventListener('mouseenter', function () {
            deplacerIndicator(lien);
        });
        lien.addEventListener('mouseleave', function () {
            if (navLienActif) deplacerIndicator(navLienActif);
        });
    });

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

// Lightbox images
    var overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';
    var lbImg = document.createElement('img');
    overlay.appendChild(lbImg);
    document.body.appendChild(overlay);

    overlay.addEventListener('click', function () {
        overlay.classList.remove('visible');
    });

// Fiche overlay pour avatars
    var ficheOverlay = document.createElement('div');
    ficheOverlay.className = 'lightbox-overlay';
    ficheOverlay.style.cursor = 'pointer';
    var ficheCard = document.createElement('div');
    ficheCard.style.cssText = 'background:var(--surface);border-radius:16px;padding:2rem;max-width:400px;width:100%;text-align:center;cursor:default;animation:modale-entree .25s ease-out';
    ficheOverlay.appendChild(ficheCard);
    document.body.appendChild(ficheOverlay);

    ficheOverlay.addEventListener('click', function(e) {
        if (e.target === ficheOverlay) ficheOverlay.classList.remove('visible');
    });

    function ouvrirFiche(ligne) {
        var imgSrc = ligne.dataset.photoFull || ligne.dataset.photo;
        var nom = (ligne.dataset.civilite || '') + ' ' + (ligne.dataset.nom || '') + ' ' + (ligne.dataset.prenom || '');
        var lieu = ligne.dataset.lieu || '';
        var province = ligne.dataset.province || '';
        var mail = ligne.dataset.mail || '';
        var matricule = ligne.dataset.matricule || '';
        ficheCard.innerHTML =
            '<div style="width:140px;height:140px;border-radius:50%;overflow:hidden;margin:0 auto 1rem;background:var(--fond)"><img src="' + imgSrc + '" style="width:100%;height:100%;object-fit:cover"></div>' +
            '<h3 style="margin:0 0 .4rem;font-size:1.15rem">' + nom + '</h3>' +
            '<p style="color:var(--txt-2);font-size:.88rem;margin:0 0 1rem">' + mail + '</p>' +
            '<div style="display:flex;gap:1rem;justify-content:center;font-size:.85rem;color:var(--txt-2)">' +
            '<span>' + lieu + (province ? ' (' + province + ')' : '') + '</span>' +
            '<span>#' + matricule + '</span>' +
            '</div>';
        ficheOverlay.classList.add('visible');
    }

    document.addEventListener('click', function (e) {
        var avatar = e.target.closest('.avatar-employe');
        if (avatar) {
            e.preventDefault();
            e.stopPropagation();
            var ligne = avatar.closest('.emp-ligne');
            if (ligne) { ouvrirFiche(ligne); return; }
        }
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

        if (fichier.size > 8 * 1024 * 1024) {
            alert('La photo ne doit pas depasser 8 Mo.');
            input.value = '';
            return;
        }

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
                    input.value = '';
                }
            } else {
                var msg = 'Erreur serveur.';
                try {
                    var r = JSON.parse(xhr.responseText);
                    if (r && r.message) msg = r.message;
                } catch (e) {
                    if (xhr.responseText) msg = xhr.responseText;
                }
                alert(msg);
                input.value = '';
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

// Messages flash auto-dismiss
    document.querySelectorAll('[data-autoferme]').forEach(function(el) {
        setTimeout(function() {
            el.style.transition = 'opacity .4s, transform .4s';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(function() { el.remove(); }, 400);
        }, 4000);
    });

// Pages modales — clic sur fond ou Escape → retour
    var pageModale = document.querySelector('.page-modale');
    if (pageModale) {
        var fond = pageModale.querySelector('.page-modale-fond');
        if (fond) {
            fond.addEventListener('click', function() {
                window.history.back();
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') window.history.back();
        });
    }
});
