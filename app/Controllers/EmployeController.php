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
            flash('erreur', 'Acces reserve aux developpeurs et administrateurs.');
            $this->redirect('');
        }
        $this->employes = new EmployeModel();
        $this->lieux = new LieuModel();
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $terme = trim((string) ($_GET['q'] ?? ''));

        if ($terme !== '') {
            try {
                $resultats = $this->employes->search($terme);
            } catch (\Throwable $e) {
                $resultats = [];
            }
            $donnees = ['data' => $resultats, 'total' => count($resultats), 'page' => 1, 'pages' => 1];
        } else {
            $donnees = $this->employes->paginate($page, 10);
        }

        $this->view('employes/index', [
            'employes' => $donnees['data'],
            'page'     => $donnees['page'],
            'pages'    => $donnees['pages'],
            'total'    => $donnees['total'],
            'terme'    => $terme,
        ]);
    }

    public function jamaisAffectes(): void
    {
        $this->view('employes/jamais-affectes', [
            'employes' => $this->employes->jamaisAffectes(),
        ]);
    }

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

    public function creer(): void
    {
        $this->view('employes/form', [
            'employe'   => null,
            'erreurs'   => [],
            'anciennes' => [],
            'lieux'     => $this->lieux->all(),
        ]);
    }

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
