<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validator;
use App\Models\AffectationModel;
use App\Models\EmployeModel;
use App\Models\LieuModel;

final class EmployeController extends Controller
{
    private EmployeModel $employes;
    private LieuModel $lieux;

    public function __construct()
    {
        if (!has_role('developpeur', 'administrateur')) {
            flash('erreur', 'Vous n\'avez pas acces a cette page.');
            $this->redirect('');
        }
        $this->employes = new EmployeModel();
        $this->lieux = new LieuModel();
    }

    // Affiche la liste paginee des employes.
    // Si ?jamais=1, filtre les employes sans aucune affectation.
    public function index(): void
    {
        $jamais = !empty($_GET['jamais']);

        if ($jamais) {
            $employes = $this->employes->jamaisAffectes();
            $donnees = ['data' => $employes, 'total' => count($employes), 'page' => 1, 'pages' => 1];
        } else {
            $page = max(1, (int) ($_GET['page'] ?? 1));
            $donnees = $this->employes->paginate($page, 10);
        }

        $this->view('employes/index', [
            'employes' => $donnees['data'],
            'page'     => $donnees['page'],
            'pages'    => $donnees['pages'],
            'total'    => $donnees['total'],
            'terme'    => '',
            'jamais'   => $jamais,
        ]);
    }

    // Affiche la liste paginee des employes.
    public function historique(string $numEmp): void
    {
        $employe = $this->employes->find((int) $numEmp);

        if ($employe === null) {
            flash('erreur', 'Employe introuvable.');
            $this->redirect('employes');
        }

        $this->view('employes/historique', [
            'employe'      => $employe,
            'affectations' => (new AffectationModel())->historiqueParEmploye((int) $numEmp),
        ]);
    }

    // Affiche le formulaire d'ajout d'un employe.
    public function creer(): void
    {
        $this->view('employes/form', [
            'employe'   => null,
            'erreurs'   => [],
            'anciennes' => [],
            'lieux'     => $this->lieux->all(),
        ]);
    }

    // Valide les donnees et cree un nouvel employe.
    public function enregistrer(): void
    {
        csrf_verify();

        $donnees = $this->donneesFormulaire();

        $validator = $this->valider($donnees);

        if (!$validator->fails() && $this->employes->existeDejaMail($donnees['mail'])) {
            $validator->addError('mail', 'Cet email est deja utilise par un autre employe.');
        }

        if ($validator->fails()) {
            $this->view('employes/form', [
                'employe' => null, 'erreurs' => $validator->errors(), 'anciennes' => $donnees, 'lieux' => $this->lieux->all(),
            ]);

            return;
        }

        $this->employes->create($donnees);

        flash('succes', "L'employe a ete ajoute avec succes.");
        $this->redirect('employes');
    }

    // Affiche le formulaire de modification d'un employe.
    public function editer(string $numEmp): void
    {
        $employe = $this->employes->find((int) $numEmp);

        if ($employe === null) {
            flash('erreur', 'Employe introuvable.');
            $this->redirect('employes');
        }

        $this->view('employes/form', [
            'employe' => $employe, 'erreurs' => [], 'anciennes' => $employe, 'lieux' => $this->lieux->all(),
        ]);
    }

    // Valide et met a jour les donnees d'un employe existant.
    public function mettreAJour(string $numEmp): void
    {
        csrf_verify();

        $id = (int) $numEmp;
        $employe = $this->employes->find($id);

        if ($employe === null) {
            flash('erreur', 'Employe introuvable.');
            $this->redirect('employes');
        }

        $donnees = $this->donneesFormulaire();
        $validator = $this->valider($donnees);

        if (!$validator->fails() && $this->employes->existeDejaMail($donnees['mail'], $id)) {
            $validator->addError('mail', 'Cet email est deja utilise par un autre employe.');
        }

        if ($validator->fails()) {
            $this->view('employes/form', [
                'employe' => $employe, 'erreurs' => $validator->errors(), 'anciennes' => $donnees, 'lieux' => $this->lieux->all(),
            ]);

            return;
        }

        $this->employes->update($id, $donnees);

        flash('succes', "L'employe a ete modifie avec succes.");
        $this->redirect('employes');
    }

    // Valide et televerse une photo pour un employe (requete AJAX).
    public function uploadPhoto(string $numEmp): void
    {
        csrf_verify();

        $id = (int) $numEmp;
        $employe = $this->employes->find($id);

        if ($employe === null) {
            $this->json(['success' => false, 'message' => 'Employe introuvable.'], 404);
            return;
        }

        if (empty($_FILES['photo']['name'])) {
            $this->json(['success' => false, 'message' => 'Aucun fichier fourni.'], 400);
            return;
        }

        $fichier = $_FILES['photo'];

        if ($fichier['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => "Erreur lors de l'envoi du fichier."], 400);
            return;
        }

        $extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $extensions, true)) {
            $this->json(['success' => false, 'message' => 'Format non autorise (jpg, png, webp).'], 400);
            return;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($fichier['tmp_name']);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($mimeType, $allowedMimes, true)) {
            $this->json(['success' => false, 'message' => 'Type de fichier invalide.'], 400);
            return;
        }

        if ($fichier['size'] > 2 * 1024 * 1024) {
            $this->json(['success' => false, 'message' => 'La photo ne doit pas depasser 2 Mo.'], 400);
            return;
        }

        $dossier = dirname(__DIR__, 2) . '/public/uploads';
        $nomFichier = 'emp_' . $id . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
        $destination = $dossier . '/' . $nomFichier;

        if (!move_uploaded_file($fichier['tmp_name'], $destination)) {
            $this->json(['success' => false, 'message' => "Echec de l'enregistrement du fichier."], 500);
            return;
        }

        if (!empty($employe['photo']) && is_file($dossier . '/' . $employe['photo'])) {
            unlink($dossier . '/' . $employe['photo']);
        }

        $this->employes->updatePhoto($id, $nomFichier);

        $this->json(['success' => true, 'photo' => $nomFichier]);
    }

    // Supprime un employe et sa photo associee.
    public function supprimer(string $numEmp): void
    {
        csrf_verify();

        $id = (int) $numEmp;
        $employe = $this->employes->find($id);

        if ($employe !== null && !empty($employe['photo'])) {
            $dossier = dirname(__DIR__, 2) . '/public/uploads';
            $chemin = $dossier . '/' . $employe['photo'];
            if (is_file($chemin)) {
                unlink($chemin);
            }
        }

        $this->employes->delete($id);

        flash('succes', "L'employe a ete supprime.");
        $this->redirect('employes');
    }

    // Extrait et retourne les donnees du formulaire employe.
    private function donneesFormulaire(): array
    {
        return [
            'civilite' => $_POST['civilite'] ?? '',
            'nom'      => trim($_POST['nom'] ?? ''),
            'prenom'   => trim($_POST['prenom'] ?? ''),
            'mail'     => trim($_POST['mail'] ?? ''),
            'poste'    => trim($_POST['poste'] ?? ''),
            'id_lieu'  => (int) ($_POST['id_lieu'] ?? 0),
        ];
    }

    // Configure et retourne le validateur pour les donnees employe.
    private function valider(array $donnees): Validator
    {
        $validator = new Validator($donnees);

        $validator
            ->in('civilite', ['Mr', 'Mlle', 'Mme'], 'La civilite')
            ->required('civilite', 'La civilite')
            ->required('nom', 'Le nom')
            ->required('prenom', 'Le prenom')
            ->required('mail', "L'email")
            ->email('mail')
            ->required('poste', 'Le poste');

        if (empty($donnees['id_lieu'])) {
            $validator->addError('id_lieu', 'Veuillez selectionner un lieu.');
        }

        return $validator;
    }
}
