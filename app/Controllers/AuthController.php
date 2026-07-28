<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Mailer;
use App\Helpers\Validator;
use App\Models\JetonConnexionModel;
use App\Models\ReinitialisationMdpModel;
use App\Models\TentativeConnexionModel;
use App\Models\UtilisateurModel;

final class AuthController extends Controller
{
    private UtilisateurModel $utilisateurs;
    private array $config;

    public function __construct()
    {
        $this->utilisateurs = new UtilisateurModel();
        $this->config = require dirname(__DIR__, 2) . '/config/config.php';
    }

    // Inscription

    public function afficherInscription(): void
    {
        $this->view('auth/register', ['erreurs' => [], 'anciennes' => []], layout: 'layouts/auth');
    }

    public function inscrire(): void
    {
        csrf_verify();

        $donnees = [
            'nom'                  => trim($_POST['nom'] ?? ''),
            'prenom'               => trim($_POST['prenom'] ?? ''),
            'email'                => trim($_POST['email'] ?? ''),
            'mot_de_passe'         => $_POST['mot_de_passe'] ?? '',
            'confirmation_mdp'     => $_POST['confirmation_mdp'] ?? '',
        ];

        $validator = new Validator($donnees);
        $validator
            ->required('nom', 'Le nom')
            ->required('prenom', 'Le prenom')
            ->required('email', "L'email")
            ->email('email')
            ->required('mot_de_passe', 'Le mot de passe')
            ->password('mot_de_passe')
            ->matches('confirmation_mdp', 'mot_de_passe', 'La confirmation du mot de passe');

        if (!$validator->fails() && $this->utilisateurs->existeDejaEmail($donnees['email'])) {
            $validator->addError('email', 'Cet email est deja utilise.');
        }

        if ($validator->fails()) {
            $this->view('auth/register', [
                'erreurs'   => $validator->errors(),
                'anciennes' => $donnees,
            ], layout: 'layouts/auth');

            return;
        }

        $this->utilisateurs->create([
            'nom'          => $donnees['nom'],
            'prenom'       => $donnees['prenom'],
            'email'        => $donnees['email'],
            'mot_de_passe' => $donnees['mot_de_passe'],
            'role'         => 'administrateur',
            'statut'       => 'actif',
        ]);

        flash('succes', 'Votre compte a ete cree. Vous pouvez maintenant vous connecter.');
        $this->redirect('connexion');
    }

    // Connexion / Deconnexion

    public function afficherConnexion(): void
    {
        $this->view('auth/login', ['erreurs' => [], 'ancienEmail' => ''], layout: 'layouts/auth');
    }

    public function connecter(): void
    {
        csrf_verify();

        $email = trim($_POST['email'] ?? '');
        $motDePasse = $_POST['mot_de_passe'] ?? '';
        $seSouvenir = !empty($_POST['se_souvenir']);
        $adresseIp = $_SERVER['REMOTE_ADDR'] ?? 'inconnue';

        $tentatives = new TentativeConnexionModel();
        $maxTentatives = $this->config['security']['max_login_attempts'];
        $dureeBlocage = $this->config['security']['lockout_minutes'];

        if ($tentatives->nombreEchecsRecents($email, $adresseIp, $dureeBlocage) >= $maxTentatives) {
            $this->view('auth/login', [
                'erreurs'     => ['general' => "Trop de tentatives. Reessayez dans {$dureeBlocage} minutes."],
                'ancienEmail' => $email,
            ], layout: 'layouts/auth');

            return;
        }

        $utilisateur = $this->utilisateurs->findByEmail($email);
        $motDePasseValide = $utilisateur !== null && password_verify($motDePasse, $utilisateur['mot_de_passe']);

        $tentatives->enregistrer($email, $adresseIp, $motDePasseValide);

        if (!$motDePasseValide) {
            $this->view('auth/login', [
                'erreurs'     => ['general' => 'Email ou mot de passe incorrect.'],
                'ancienEmail' => $email,
            ], layout: 'layouts/auth');

            return;
        }

        if ($utilisateur['statut'] !== 'actif') {
            $this->view('auth/login', [
                'erreurs'     => ['general' => 'Ce compte a ete desactive. Contactez un administrateur.'],
                'ancienEmail' => $email,
            ], layout: 'layouts/auth');

            return;
        }

        $this->ouvrirSession($utilisateur);

        if ($seSouvenir) {
            $this->creerCookieRememberMe((int) $utilisateur['id']);
        }

        $this->redirect('');
    }

    private function ouvrirSession(array $utilisateur): void
    {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'     => $utilisateur['id'],
            'nom'    => $utilisateur['nom'],
            'prenom' => $utilisateur['prenom'],
            'email'  => $utilisateur['email'],
            'role'   => $utilisateur['role'],
            'photo'  => $utilisateur['photo'],
        ];
        $_SESSION['derniere_activite'] = time();
    }

    private function creerCookieRememberMe(int $utilisateurId): void
    {
        $selecteur = bin2hex(random_bytes(8));
        $validateur = bin2hex(random_bytes(32));
        $jours = $this->config['security']['remember_me_days'];

        (new JetonConnexionModel())->creer(
            $utilisateurId,
            $selecteur,
            hash('sha256', $validateur),
            date('Y-m-d H:i:s', time() + $jours * 86400)
        );

        setcookie(
            'remember_token',
            $selecteur . ':' . $validateur,
            [
                'expires'  => time() + $jours * 86400,
                'path'     => '/',
                'secure'   => !empty($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    public function deconnecter(): void
    {
        csrf_verify();

        if (isset($_SESSION['user']['id'])) {
            (new JetonConnexionModel())->supprimerPourUtilisateur((int) $_SESSION['user']['id']);
        }

        setcookie('remember_token', '', time() - 3600, '/');
        session_unset();
        session_destroy();

        $this->redirect('connexion');
    }

    // Mot de passe oublie

    public function afficherMotDePasseOublie(): void
    {
        $this->view('auth/forgot-password', ['message' => null], layout: 'layouts/auth');
    }

    public function envoyerLienReinitialisation(): void
    {
        csrf_verify();

        $email = trim($_POST['email'] ?? '');
        $utilisateur = $this->utilisateurs->findByEmail($email);

        // Message identique que l'email existe ou non : evite de divulguer
        // quels emails sont enregistres dans l'application.
        $messageGenerique = "Si cet email existe dans notre systeme, un lien de reinitialisation vient d'etre envoye.";

        if ($utilisateur !== null) {
            $jetonClair = bin2hex(random_bytes(32));
            $jetonHash = hash('sha256', $jetonClair);

            (new ReinitialisationMdpModel())->creer(
                (int) $utilisateur['id'],
                $jetonHash,
                date('Y-m-d H:i:s', time() + 3600)
            );

            $lien = url('reinitialiser-mot-de-passe/' . $jetonClair);

            Mailer::envoyer(
                $utilisateur['email'],
                'Reinitialisation de votre mot de passe',
                "<p>Bonjour {$utilisateur['prenom']},</p>
                 <p>Cliquez sur le lien suivant pour reinitialiser votre mot de passe (valable 1 heure) :</p>
                 <p><a href=\"{$lien}\">{$lien}</a></p>
                 <p>Si vous n'etes pas a l'origine de cette demande, ignorez cet email.</p>"
            );
        }

        $this->view('auth/forgot-password', ['message' => $messageGenerique], layout: 'layouts/auth');
    }

    public function afficherReinitialisation(string $jeton): void
    {
        $jetonHash = hash('sha256', $jeton);
        $demande = (new ReinitialisationMdpModel())->trouverValide($jetonHash);

        if ($demande === null) {
            flash('erreur', 'Ce lien de reinitialisation est invalide ou a expire.');
            $this->redirect('mot-de-passe-oublie');
        }

        $this->view('auth/reset-password', ['jeton' => $jeton, 'erreurs' => []], layout: 'layouts/auth');
    }

    public function reinitialiser(): void
    {
        csrf_verify();

        $jeton = $_POST['jeton'] ?? '';
        $jetonHash = hash('sha256', $jeton);
        $demande = (new ReinitialisationMdpModel())->trouverValide($jetonHash);

        if ($demande === null) {
            flash('erreur', 'Ce lien de reinitialisation est invalide ou a expire.');
            $this->redirect('mot-de-passe-oublie');
        }

        $validator = new Validator($_POST);
        $validator
            ->required('mot_de_passe', 'Le mot de passe')
            ->password('mot_de_passe')
            ->matches('confirmation_mdp', 'mot_de_passe', 'La confirmation du mot de passe');

        if ($validator->fails()) {
            $this->view('auth/reset-password', [
                'jeton'   => $jeton,
                'erreurs' => $validator->errors(),
            ], layout: 'layouts/auth');

            return;
        }

        $this->utilisateurs->updateMotDePasse((int) $demande['utilisateur_id'], $_POST['mot_de_passe']);
        (new ReinitialisationMdpModel())->marquerUtilise((int) $demande['id']);
        (new JetonConnexionModel())->supprimerPourUtilisateur((int) $demande['utilisateur_id']);

        flash('succes', 'Votre mot de passe a ete reinitialise. Vous pouvez vous connecter.');
        $this->redirect('connexion');
    }

    // Profil : informations personnelles + photo

    public function afficherProfil(): void
    {
        $utilisateur = $this->utilisateurs->find((int) $_SESSION['user']['id']);

        $this->view('auth/profil', [
            'utilisateur' => $utilisateur,
            'erreurs'     => [],
        ]);
    }

    public function mettreAJourProfil(): void
    {
        csrf_verify();

        $id = (int) $_SESSION['user']['id'];
        $utilisateur = $this->utilisateurs->find($id);

        $donnees = [
            'nom'    => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email'  => trim($_POST['email'] ?? ''),
            'role'   => $utilisateur['role'], // le role ne se modifie pas depuis son propre profil
        ];

        $validator = new Validator($donnees);
        $validator
            ->required('nom', 'Le nom')
            ->required('prenom', 'Le prenom')
            ->required('email', "L'email")
            ->email('email');

        if (!$validator->fails() && $this->utilisateurs->existeDejaEmail($donnees['email'], $id)) {
            $validator->addError('email', 'Cet email est deja utilise par un autre compte.');
        }

        if ($validator->fails()) {
            $this->view('auth/profil', [
                'utilisateur' => array_merge($utilisateur, $donnees),
                'erreurs'     => $validator->errors(),
            ]);

            return;
        }

        $this->utilisateurs->update($id, $donnees);

        if (!empty($_FILES['photo']['name'])) {
            $this->traiterPhoto($id, $utilisateur);
        }

        // Met a jour les informations affichees dans la session (navbar, sidebar).
        $utilisateur = $this->utilisateurs->find($id);
        $_SESSION['user']['nom']    = $utilisateur['nom'];
        $_SESSION['user']['prenom'] = $utilisateur['prenom'];
        $_SESSION['user']['email']  = $utilisateur['email'];
        $_SESSION['user']['photo']  = $utilisateur['photo'];

        flash('succes', 'Votre profil a ete mis a jour avec succes.');
        $this->redirect('profil');
    }

    private function traiterPhoto(int $id, array $ancienUtilisateur): void
    {
        $fichier = $_FILES['photo'];

        if ($fichier['error'] !== UPLOAD_ERR_OK) {
            flash('erreur', "Le televersement de la photo a echoue.");

            return;
        }

        $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
        $tailleMaxOctets = 2 * 1024 * 1024; // 2 Mo

        if (!in_array($extension, $extensionsAutorisees, true)) {
            flash('erreur', 'Format de photo non autorise (jpg, jpeg, png, webp uniquement).');

            return;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($fichier['tmp_name']);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($mimeType, $allowedMimes, true)) {
            flash('erreur', 'Type de fichier invalide.');

            return;
        }

        if ($fichier['size'] > $tailleMaxOctets) {
            flash('erreur', 'La photo ne doit pas depasser 2 Mo.');

            return;
        }

        $dossierUploads = dirname(__DIR__, 2) . '/public/uploads';
        $nomFichier = 'avatar_' . $id . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
        $cheminDestination = $dossierUploads . '/' . $nomFichier;

        if (!move_uploaded_file($fichier['tmp_name'], $cheminDestination)) {
            flash('erreur', "L'enregistrement de la photo a echoue.");

            return;
        }

        // Supprime l'ancienne photo si elle existe, pour ne pas accumuler de fichiers orphelins.
        if (!empty($ancienUtilisateur['photo']) && is_file($dossierUploads . '/' . $ancienUtilisateur['photo'])) {
            unlink($dossierUploads . '/' . $ancienUtilisateur['photo']);
        }

        $this->utilisateurs->updatePhoto($id, $nomFichier);
    }

    // Changement de mot de passe

    public function afficherChangementMotDePasse(): void
    {
        $this->view('auth/change-password', ['erreurs' => []]);
    }

    public function changerMotDePasse(): void
    {
        csrf_verify();

        $utilisateur = $this->utilisateurs->find((int) $_SESSION['user']['id']);

        $validator = new Validator($_POST);
        $validator
            ->required('ancien_mot_de_passe', "L'ancien mot de passe")
            ->required('nouveau_mot_de_passe', 'Le nouveau mot de passe')
            ->password('nouveau_mot_de_passe')
            ->matches('confirmation_mdp', 'nouveau_mot_de_passe', 'La confirmation du mot de passe');

        if (!$validator->fails() && !password_verify($_POST['ancien_mot_de_passe'], $utilisateur['mot_de_passe'])) {
            $validator->addError('ancien_mot_de_passe', "L'ancien mot de passe est incorrect.");
        }

        if ($validator->fails()) {
            $this->view('auth/change-password', ['erreurs' => $validator->errors()]);

            return;
        }

        $this->utilisateurs->updateMotDePasse((int) $utilisateur['id'], $_POST['nouveau_mot_de_passe']);

        flash('succes', 'Votre mot de passe a ete modifie avec succes.');
        $this->redirect('profil/mot-de-passe');
    }
}
