<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Mailer;
use App\Helpers\Validator;
use App\Models\AffectationModel;
use App\Models\EmployeModel;
use App\Models\LieuModel;
use Dompdf\Dompdf;
use Dompdf\Options;

final class AffectationController extends Controller
{
    private AffectationModel $affectations;
    private EmployeModel $employes;
    private LieuModel $lieux;

    public function __construct()
    {
        if (!has_role('developpeur', 'administrateur')) {
            flash('erreur', 'Acces reserve aux developpeurs et administrateurs.');
            $this->redirect('');
        }
        $this->affectations = new AffectationModel();
        $this->employes = new EmployeModel();
        $this->lieux = new LieuModel();
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $debut = trim((string) ($_GET['debut'] ?? ''));
        $fin = trim((string) ($_GET['fin'] ?? ''));

        if ($debut !== '' && $fin !== '') {
            $resultats = $this->affectations->entreDeuxDates($debut, $fin);
            $donnees = ['data' => $resultats, 'total' => count($resultats), 'page' => 1, 'pages' => 1];
        } else {
            $donnees = $this->affectations->paginate($page, 10);
        }

        $this->view('affectations/index', [
            'affectations' => $donnees['data'],
            'page'         => $donnees['page'],
            'pages'        => $donnees['pages'],
            'total'        => $donnees['total'],
            'debut'        => $debut,
            'fin'          => $fin,
        ]);
    }

    public function historique(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $donnees = $this->affectations->historique($page, 10);

        $this->view('affectations/historique', [
            'affectations' => $donnees['data'],
            'page'         => $donnees['page'],
            'pages'        => $donnees['pages'],
            'total'        => $donnees['total'],
        ]);
    }

    public function viderHistorique(): void
    {
        csrf_verify();

        $this->affectations->viderHistorique();

        flash('succes', "L'historique des affectations a ete vide.");
        $this->redirect('affectations/historique');
    }

    public function creer(): void
    {
        $this->view('affectations/form', [
            'erreurs'           => [],
            'anciennes'         => [],
            'employes'          => $this->employes->all(),
            'lieux'             => $this->lieux->all(),
            'prochainNumero'    => $this->affectations->prochainNumeroArrete(),
        ]);
    }

    public function creerMultiple(): void
    {
        $this->view('affectations/form-multiple', [
            'erreurs'           => [],
            'anciennes'         => [],
            'employes'          => $this->employes->all(),
            'lieux'             => $this->lieux->all(),
        ]);
    }

    public function enregistrerMultiple(): void
    {
        csrf_verify();

        $employeIds = $_POST['num_employes'] ?? [];
        $nouveauLieuId = (int) ($_POST['nouveau_lieu_id'] ?? 0);
        $dateAffect = $_POST['date_affect'] ?? '';
        $datePriseService = $_POST['date_prise_service'] ?? '';
        $raison = trim($_POST['raison'] ?? '');
        $notifier = !empty($_POST['notifier_email']);

        $validator = new Validator([
            'nouveau_lieu_id' => $nouveauLieuId,
            'date_affect' => $dateAffect,
            'date_prise_service' => $datePriseService,
        ]);
        $validator
            ->required('date_affect', "La date de l'arrete")
            ->required('date_prise_service', 'La date de prise de service');

        if (empty($employeIds)) {
            $validator->addError('num_employes', 'Veuillez selectionner au moins un employe.');
        }
        if ($nouveauLieuId === 0) {
            $validator->addError('nouveau_lieu_id', 'Veuillez selectionner le nouveau lieu.');
        }
        if (!$validator->fails() && $datePriseService < $dateAffect) {
            $validator->addError('date_prise_service', "La prise de service ne peut preceder la date de l'arrete.");
        }

        if ($validator->fails()) {
            $this->view('affectations/form-multiple', [
                'erreurs' => $validator->errors(),
                'anciennes' => $_POST,
                'employes' => $this->employes->all(),
                'lieux' => $this->lieux->all(),
            ]);

            return;
        }

        $nbEmployesValid = 0;
        foreach ($employeIds as $numEmp) {
            $numEmp = (int) $numEmp;
            $employe = $this->employes->find($numEmp);
            if ($employe !== null && (int) $employe['id_lieu'] !== $nouveauLieuId) {
                $nbEmployesValid++;
            }
        }

        $numerosLibres = $this->affectations->numerosArretesLibres($nbEmployesValid);

        $compteur = 0;
        $idxNumero = 0;
        foreach ($employeIds as $numEmp) {
            $numEmp = (int) $numEmp;
            $employe = $this->employes->find($numEmp);

            if ($employe === null) {
                continue;
            }

            if ((int) $employe['id_lieu'] === $nouveauLieuId) {
                continue;
            }

            $donnees = [
                'numero_arrete'      => $numerosLibres[$idxNumero],
                'num_emp'            => $numEmp,
                'ancien_lieu_id'     => $employe['id_lieu'],
                'nouveau_lieu_id'    => $nouveauLieuId,
                'date_affect'        => $dateAffect,
                'date_prise_service' => $datePriseService,
                'raison'             => $raison !== '' ? $raison : null,
            ];

            $idAffectation = $this->affectations->create($donnees);

            if ($notifier) {
                $this->envoyerNotification($idAffectation, $employe);
            }

            $idxNumero++;
            $compteur++;
        }

        flash('succes', $compteur . ' affectation(s) enregistree(s) avec succes.');
        $this->redirect('affectations');
    }

    public function enregistrer(): void
    {
        csrf_verify();

        $numeroArrete = trim($_POST['numero_arrete'] ?? '');
        if ($numeroArrete === '') {
            $numeroArrete = $this->affectations->prochainNumeroArrete();
        }

        $donnees = [
            'numero_arrete'      => $numeroArrete,
            'num_emp'            => (int) ($_POST['num_emp'] ?? 0),
            'nouveau_lieu_id'    => (int) ($_POST['nouveau_lieu_id'] ?? 0),
            'date_affect'        => $_POST['date_affect'] ?? '',
            'date_prise_service' => $_POST['date_prise_service'] ?? '',
            'raison'             => trim($_POST['raison'] ?? '') ?: null,
        ];
        $notifier = !empty($_POST['notifier_email']);

        $validator = new Validator($donnees);
        $validator
            ->required('numero_arrete', "Le numero d'arrete")
            ->required('date_affect', "La date de l'arrete")
            ->required('date_prise_service', 'La date de prise de service');

        if (empty($donnees['num_emp'])) {
            $validator->addError('num_emp', "Veuillez selectionner un employe.");
        }
        if (empty($donnees['nouveau_lieu_id'])) {
            $validator->addError('nouveau_lieu_id', 'Veuillez selectionner le nouveau lieu.');
        }
        if (!$validator->fails() && $donnees['date_prise_service'] < $donnees['date_affect']) {
            $validator->addError('date_prise_service', "La prise de service ne peut preceder la date de l'arrete.");
        }
        if (!$validator->fails() && $this->affectations->existeDejaNumeroArrete($donnees['numero_arrete'])) {
            $validator->addError('numero_arrete', "Ce numero d'arrete est deja utilise.");
        }

        $employe = $donnees['num_emp'] ? $this->employes->find($donnees['num_emp']) : null;

        if (!$validator->fails() && $employe === null) {
            $validator->addError('num_emp', 'Employe introuvable.');
        }

        if (!$validator->fails() && $employe !== null && (int) $employe['id_lieu'] === $donnees['nouveau_lieu_id']) {
            $validator->addError('nouveau_lieu_id', "L'employe est deja affecte a ce lieu.");
        }

        if ($validator->fails()) {
            $this->view('affectations/form', [
                'erreurs' => $validator->errors(), 'anciennes' => $donnees,
                'employes' => $this->employes->all(), 'lieux' => $this->lieux->all(),
            ]);

            return;
        }

        // Affectation de l'ancien lieu
        $donnees['ancien_lieu_id'] = $employe['id_lieu'];

        $idAffectation = $this->affectations->create($donnees);

        if ($notifier) {
            $this->envoyerNotification($idAffectation, $employe);
        }

        flash('succes', "L'affectation a ete enregistree avec succes.");
        $this->redirect('affectations');
    }

    private function envoyerNotification(int $idAffectation, array $employe): void
    {
        $affectation = $this->affectations->find($idAffectation);

        $corps = "<p>Bonjour {$employe['civilite']} {$employe['nom']} {$employe['prenom']},</p>
            <p>Nous vous informons que vous etes affecte(e) a <strong>{$affectation['nouveau_lieu_designation']}</strong>,
            pour compter de votre date de prise de service du
            <strong>" . date('d/m/Y', strtotime($affectation['date_prise_service'])) . "</strong>.</p>
            <p>Reference de l'arrete : N°{$affectation['numero_arrete']}</p>
            <p>Cordialement.</p>";

        $envoye = Mailer::envoyer($employe['mail'], 'Notification d\'affectation', $corps);

        if ($envoye) {
            $this->affectations->marquerNotifiee($idAffectation);
        }
    }

    public function notifier(string $id): void
    {
        csrf_verify();

        $idAffectation = (int) $id;
        $affectation = $this->affectations->find($idAffectation);

        if ($affectation === null) {
            flash('erreur', 'Affectation introuvable.');
            $this->redirect('affectations');
        }

        $employe = $this->employes->find((int) $affectation['num_emp']);

        if ($employe === null) {
            flash('erreur', 'Employe introuvable.');
            $this->redirect('affectations');
        }

        $this->envoyerNotification($idAffectation, $employe);

        flash('succes', "L'employe a ete notifie par email.");

        $from = $_GET['from'] ?? '';
        $this->redirect($from === 'historique' ? 'affectations/historique' : 'affectations');
    }

    public function editer(string $id): void
    {
        $affectation = $this->affectations->find((int) $id);

        if ($affectation === null) {
            flash('erreur', 'Affectation introuvable.');
            $this->redirect('affectations');
        }

        $this->view('affectations/editer', ['affectation' => $affectation, 'erreurs' => []]);
    }

    /** Seuls le numero d'arrete et les dates peuvent etre corriges (l'historique reste figé). */
    public function mettreAJour(string $id): void
    {
        csrf_verify();

        $idAffectation = (int) $id;
        $affectation = $this->affectations->find($idAffectation);

        if ($affectation === null) {
            flash('erreur', 'Affectation introuvable.');
            $this->redirect('affectations');
        }

        $donnees = [
            'numero_arrete'      => trim($_POST['numero_arrete'] ?? ''),
            'date_affect'        => $_POST['date_affect'] ?? '',
            'date_prise_service' => $_POST['date_prise_service'] ?? '',
        ];

        $validator = new Validator($donnees);
        $validator
            ->required('numero_arrete', "Le numero d'arrete")
            ->required('date_affect', "La date de l'arrete")
            ->required('date_prise_service', 'La date de prise de service');

        if (!$validator->fails() && $donnees['date_prise_service'] < $donnees['date_affect']) {
            $validator->addError('date_prise_service', "La prise de service ne peut preceder la date de l'arrete.");
        }
        if (!$validator->fails() && $this->affectations->existeDejaNumeroArrete($donnees['numero_arrete'], $idAffectation)) {
            $validator->addError('numero_arrete', "Ce numero d'arrete est deja utilise.");
        }

        if ($validator->fails()) {
            $this->view('affectations/editer', ['affectation' => array_merge($affectation, $donnees), 'erreurs' => $validator->errors()]);

            return;
        }

        $this->affectations->update($idAffectation, $donnees);

        flash('succes', "L'affectation a ete modifiee avec succes.");
        $this->redirect('affectations');
    }

    public function supprimer(string $id): void
    {
        csrf_verify();

        $idAffectation = (int) $id;
        $affectation = $this->affectations->find($idAffectation);

        if ($affectation === null) {
            flash('erreur', 'Affectation introuvable.');
            $this->redirect('affectations');
        }

        $numEmp = (int) $affectation['num_emp'];

        $this->affectations->delete($idAffectation);

        // Restaurer le lieu de l'employe apres suppression
        $derniere = $this->affectations->derniereAffectation($numEmp, $idAffectation);
        $lieuARestaurer = $derniere !== null
            ? (int) $derniere['nouveau_lieu_id']
            : (int) $affectation['ancien_lieu_id'];

        if ($lieuARestaurer > 0) {
            $this->employes->updateLieu($numEmp, $lieuARestaurer);
        }

        flash('succes', "L'affectation a ete deplacee dans l'historique.");
        $this->redirect('affectations');
    }

    //PDF
    public function pdf(string $id): void
    {
        $affectation = $this->affectations->find((int) $id);

        if ($affectation === null) {
            flash('erreur', 'Affectation introuvable.');
            $this->redirect('affectations');
        }

        ob_start();
        require dirname(__DIR__) . '/Views/affectations/arrete-pdf.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream(
            'arrete-' . $affectation['numero_arrete'] . '.pdf',
            ['Attachment' => false]
        );
        exit;
    }

    /** Imprimer tout l'historique des affectations supprimees. */
    public function imprimer(): void
    {
        $affectations = $this->affectations->toutHistorique();

        $dateMin = null;
        $dateMax = null;
        if (!empty($affectations)) {
            $dateMin = $affectations[count($affectations) - 1]['date_affect'];
            $dateMax = $affectations[0]['date_affect'];
        }

        ob_start();
        require dirname(__DIR__) . '/Views/affectations/historique-pdf.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $dompdf->stream(
            'historique-affectations.pdf',
            ['Attachment' => true]
        );
        exit;
    }
}
