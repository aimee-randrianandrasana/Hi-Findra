<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validator;
use App\Models\LieuModel;

final class LieuController extends Controller
{
    private LieuModel $lieux;

    public function __construct()
    {
        if (!has_role('administrateur', 'gestionnaire')) {
            flash('erreur', 'Acces reserve aux administrateurs et gestionnaires.');
            $this->redirect('');
        }
        $this->lieux = new LieuModel();
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $terme = trim((string) ($_GET['q'] ?? ''));

        if ($terme !== '') {
            try {
                $resultats = $this->lieux->search($terme);
            } catch (\Throwable $e) {
                $resultats = [];
            }
            $donnees = ['data' => $resultats, 'total' => count($resultats), 'page' => 1, 'pages' => 1];
        } else {
            $donnees = $this->lieux->paginate($page, 10);
        }

        $this->view('lieux/index', [
            'lieux' => $donnees['data'],
            'page'  => $donnees['page'],
            'pages' => $donnees['pages'],
            'total' => $donnees['total'],
            'terme' => $terme,
        ]);
    }

    public function creer(): void
    {
        $this->view('lieux/form', ['lieu' => null, 'erreurs' => [], 'anciennes' => []]);
    }

    public function enregistrer(): void
    {
        csrf_verify();

        $donnees = [
            'designation' => trim($_POST['designation'] ?? ''),
            'province'    => trim($_POST['province'] ?? ''),
        ];

        $validator = new Validator($donnees);
        $validator
            ->required('designation', 'La designation')
            ->required('province', 'La province')
            ->in('province', ['Analamanga', 'Matsiatra Ambony', 'Atsimo-Andrefana', 'Boeny', 'Atsinanana', 'Antsiranana'], 'La province');

        if (!$validator->fails() && $this->lieux->existeDejaDesignationProvince($donnees['designation'], $donnees['province'])) {
            $validator->addError('designation', 'Ce lieu existe deja pour cette province.');
        }

        if ($validator->fails()) {
            $this->view('lieux/form', ['lieu' => null, 'erreurs' => $validator->errors(), 'anciennes' => $donnees]);

            return;
        }

        $this->lieux->create($donnees);

        flash('succes', 'Le lieu a ete ajoute avec succes.');
        $this->redirect('lieux');
    }

    public function editer(string $id): void
    {
        $lieu = $this->lieux->find((int) $id);

        if ($lieu === null) {
            flash('erreur', 'Lieu introuvable.');
            $this->redirect('lieux');
        }

        $this->view('lieux/form', ['lieu' => $lieu, 'erreurs' => [], 'anciennes' => $lieu]);
    }

    public function mettreAJour(string $id): void
    {
        csrf_verify();

        $idLieu = (int) $id;
        $lieu = $this->lieux->find($idLieu);

        if ($lieu === null) {
            flash('erreur', 'Lieu introuvable.');
            $this->redirect('lieux');
        }

        $donnees = [
            'designation' => trim($_POST['designation'] ?? ''),
            'province'    => trim($_POST['province'] ?? ''),
        ];

        $validator = new Validator($donnees);
        $validator
            ->required('designation', 'La designation')
            ->required('province', 'La province')
            ->in('province', ['Analamanga', 'Matsiatra Ambony', 'Atsimo-Andrefana', 'Boeny', 'Atsinanana', 'Antsiranana'], 'La province');

        if (!$validator->fails() && $this->lieux->existeDejaDesignationProvince($donnees['designation'], $donnees['province'], $idLieu)) {
            $validator->addError('designation', 'Ce lieu existe deja pour cette province.');
        }

        if ($validator->fails()) {
            $this->view('lieux/form', ['lieu' => $lieu, 'erreurs' => $validator->errors(), 'anciennes' => $donnees]);

            return;
        }

        $this->lieux->update($idLieu, $donnees);

        flash('succes', 'Le lieu a ete modifie avec succes.');
        $this->redirect('lieux');
    }

    public function supprimer(string $id): void
    {
        csrf_verify();

        $idLieu = (int) $id;

        if ($this->lieux->estUtilise($idLieu)) {
            flash('erreur', 'Impossible de supprimer ce lieu : il est utilise par au moins un employe ou une affectation.');
            $this->redirect('lieux');
        }

        $this->lieux->delete($idLieu);

        flash('succes', 'Le lieu a ete supprime.');
        $this->redirect('lieux');
    }
}
