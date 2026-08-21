<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validator;
use App\Models\UtilisateurModel;

final class UtilisateurController extends Controller
{
    private UtilisateurModel $utilisateurs;

    public function __construct()
    {
        $this->utilisateurs = new UtilisateurModel();
        $this->verifierAcces();
    }

    // Seul un administrateur peut gerer les comptes utilisateurs
    private function verifierAcces(): void
    {
        if (!has_role('developpeur')) {
            flash('erreur', 'Vous n\'avez pas acces a cette page.');
            $this->redirect('');
        }
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $terme = trim((string) ($_GET['q'] ?? ''));

        if ($terme !== '') {
            try {
                $resultats = $this->utilisateurs->search($terme);
            } catch (\Throwable $e) {
                $resultats = [];
            }
            $donnees = ['data' => $resultats, 'total' => count($resultats), 'page' => 1, 'pages' => 1];
        } else {
            $donnees = $this->utilisateurs->paginate($page, 10);
        }

        $this->view('utilisateurs/index', [
            'utilisateurs' => $donnees['data'],
            'page'         => $donnees['page'],
            'pages'        => $donnees['pages'],
            'total'        => $donnees['total'],
            'terme'        => $terme,
        ]);
    }

    public function creer(): void
    {
        $this->view('utilisateurs/form', ['utilisateur' => null, 'erreurs' => [], 'anciennes' => []]);
    }

    public function enregistrer(): void
    {
        csrf_verify();

        $donnees = [
            'nom'          => trim($_POST['nom'] ?? ''),
            'prenom'       => trim($_POST['prenom'] ?? ''),
            'email'        => trim($_POST['email'] ?? ''),
            'role'         => $_POST['role'] ?? 'administrateur',
            'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
        ];

        $validator = new Validator($donnees);
        $validator
            ->required('nom', 'Le nom')
            ->required('prenom', 'Le prenom')
            ->required('email', "L'email")
            ->email('email')
            ->in('role', ['developpeur', 'administrateur'], 'Le role')
            ->required('mot_de_passe', 'Le mot de passe')
            ->password('mot_de_passe');

        if (!$validator->fails() && $this->utilisateurs->existeDejaEmail($donnees['email'])) {
            $validator->addError('email', 'Cet email est deja utilise.');
        }

        if ($validator->fails()) {
            $this->view('utilisateurs/form', ['utilisateur' => null, 'erreurs' => $validator->errors(), 'anciennes' => $donnees]);

            return;
        }

        $this->utilisateurs->create([...$donnees, 'statut' => 'actif']);

        flash('succes', "L'utilisateur a ete cree avec succes.");
        $this->redirect('utilisateurs');
    }

    public function editer(string $id): void
    {
        $utilisateur = $this->utilisateurs->find((int) $id);

        if ($utilisateur === null) {
            flash('erreur', 'Utilisateur introuvable.');
            $this->redirect('utilisateurs');
        }

        $this->view('utilisateurs/form', ['utilisateur' => $utilisateur, 'erreurs' => [], 'anciennes' => $utilisateur]);
    }

    public function mettreAJour(string $id): void
    {
        csrf_verify();

        $idUtilisateur = (int) $id;
        $utilisateur = $this->utilisateurs->find($idUtilisateur);

        if ($utilisateur === null) {
            flash('erreur', 'Utilisateur introuvable.');
            $this->redirect('utilisateurs');
        }

        $donnees = [
            'nom'    => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email'  => trim($_POST['email'] ?? ''),
            'role'   => $_POST['role'] ?? 'administrateur',
        ];

        $validator = new Validator($donnees);
        $validator
            ->required('nom', 'Le nom')
            ->required('prenom', 'Le prenom')
            ->required('email', "L'email")
            ->email('email')
            ->in('role', ['developpeur', 'administrateur'], 'Le role');

        if (!$validator->fails() && $this->utilisateurs->existeDejaEmail($donnees['email'], $idUtilisateur)) {
            $validator->addError('email', 'Cet email est deja utilise.');
        }

        if ($validator->fails()) {
            $this->view('utilisateurs/form', ['utilisateur' => $utilisateur, 'erreurs' => $validator->errors(), 'anciennes' => $donnees]);

            return;
        }

        $this->utilisateurs->update($idUtilisateur, $donnees);

        flash('succes', "L'utilisateur a ete modifie avec succes.");
        $this->redirect('utilisateurs');
    }

    public function changerStatut(string $id): void
    {
        csrf_verify();

        $idUtilisateur = (int) $id;

        if ($idUtilisateur === (int) $_SESSION['user']['id']) {
            flash('erreur', 'Vous ne pouvez pas desactiver votre propre compte.');
            $this->redirect('utilisateurs');
        }

        $utilisateur = $this->utilisateurs->find($idUtilisateur);

        if ($utilisateur === null) {
            flash('erreur', 'Utilisateur introuvable.');
            $this->redirect('utilisateurs');
            return;
        }

        $nouveauStatut = $utilisateur['statut'] === 'actif' ? 'inactif' : 'actif';
        $this->utilisateurs->changerStatut($idUtilisateur, $nouveauStatut);

        flash('succes', 'Le statut du compte a ete mis a jour.');
        $this->redirect('utilisateurs');
    }

    public function supprimer(string $id): void
    {
        csrf_verify();

        $idUtilisateur = (int) $id;

        if ($idUtilisateur === (int) $_SESSION['user']['id']) {
            flash('erreur', 'Vous ne pouvez pas supprimer votre propre compte.');
            $this->redirect('utilisateurs');
        }

        $this->utilisateurs->delete($idUtilisateur);

        flash('succes', "L'utilisateur a ete supprime.");
        $this->redirect('utilisateurs');
    }
}
