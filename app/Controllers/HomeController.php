<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Models\AffectationModel;
use App\Models\EmployeModel;
use App\Models\LieuModel;

final class HomeController extends Controller
{
    public function index(): void
    {
        $employes     = new EmployeModel();
        $lieux        = new LieuModel();
        $affectations = new AffectationModel();

        $this->view('home/index', [
            'nbEmployes'     => $employes->countAll(),
            'nbLieux'        => $lieux->countAll(),
            'nbAffectations' => $affectations->countAll(),
        ]);
    }
}
