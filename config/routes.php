<?php
declare(strict_types=1);

use App\Controllers\AffectationController;
use App\Controllers\AuthController;
use App\Controllers\EmployeController;
use App\Controllers\HomeController;
use App\Controllers\LieuController;
use App\Controllers\UtilisateurController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

// @var App\Core\Router $router

// Accueil
$router->get('/', [HomeController::class, 'index'], [AuthMiddleware::class]);
$router->get('/accueil', [HomeController::class, 'index'], [AuthMiddleware::class]);

// Authentification
$router->get('/connexion',          [AuthController::class, 'afficherConnexion'],           [GuestMiddleware::class]);
$router->post('/connexion',         [AuthController::class, 'connecter'],                   [GuestMiddleware::class]);
$router->get('/inscription',        [AuthController::class, 'afficherInscription'],         [GuestMiddleware::class]);
$router->post('/inscription',       [AuthController::class, 'inscrire'],                    [GuestMiddleware::class]);
$router->get('/mot-de-passe-oublie',[AuthController::class, 'afficherMotDePasseOublie'],   [GuestMiddleware::class]);
$router->post('/mot-de-passe-oublie',[AuthController::class,'envoyerLienReinitialisation'],[GuestMiddleware::class]);
$router->get('/reinitialiser-mot-de-passe/{jeton}', [AuthController::class, 'afficherReinitialisation'], [GuestMiddleware::class]);
$router->post('/reinitialiser-mot-de-passe',        [AuthController::class, 'reinitialiser'],            [GuestMiddleware::class]);
$router->post('/deconnexion',       [AuthController::class, 'deconnecter'],                 [AuthMiddleware::class]);

// Profil
$router->get('/profil',               [AuthController::class, 'afficherProfil'],              [AuthMiddleware::class]);
$router->post('/profil',              [AuthController::class, 'mettreAJourProfil'],           [AuthMiddleware::class]);
$router->get('/profil/mot-de-passe',  [AuthController::class, 'afficherChangementMotDePasse'], [AuthMiddleware::class]);
$router->post('/profil/mot-de-passe', [AuthController::class, 'changerMotDePasse'],             [AuthMiddleware::class]);

// Lieux
$router->get('/lieux',                 [LieuController::class, 'index'],       [AuthMiddleware::class]);
$router->get('/lieux/creer',           [LieuController::class, 'creer'],       [AuthMiddleware::class]);
$router->post('/lieux',                [LieuController::class, 'enregistrer'], [AuthMiddleware::class]);
$router->get('/lieux/{id}/editer',     [LieuController::class, 'editer'],      [AuthMiddleware::class]);
$router->post('/lieux/{id}',           [LieuController::class, 'mettreAJour'],[AuthMiddleware::class]);
$router->post('/lieux/{id}/supprimer', [LieuController::class, 'supprimer'],   [AuthMiddleware::class]);

// Employes (routes litterales avant parametres dynamiques)
$router->get('/employes',                      [EmployeController::class, 'index'],          [AuthMiddleware::class]);
$router->get('/employes/creer',                [EmployeController::class, 'creer'],          [AuthMiddleware::class]);
$router->post('/employes',                     [EmployeController::class, 'enregistrer'],    [AuthMiddleware::class]);
$router->get('/employes/{id}/historique',      [EmployeController::class, 'historique'],     [AuthMiddleware::class]);
$router->get('/employes/{id}/editer',          [EmployeController::class, 'editer'],         [AuthMiddleware::class]);
$router->post('/employes/{id}',                [EmployeController::class, 'mettreAJour'],    [AuthMiddleware::class]);
$router->post('/employes/{id}/photo',          [EmployeController::class, 'uploadPhoto'],    [AuthMiddleware::class]);
$router->post('/employes/{id}/supprimer',      [EmployeController::class, 'supprimer'],      [AuthMiddleware::class]);

// Affectations
$router->get('/affectations',                   [AffectationController::class, 'index'],      [AuthMiddleware::class]);
$router->get('/affectations/historique',         [AffectationController::class, 'historique'],[AuthMiddleware::class]);
$router->get('/affectations/historique/imprimer', [AffectationController::class, 'imprimer'],[AuthMiddleware::class]);
$router->post('/affectations/historique/vider',  [AffectationController::class, 'viderHistorique'],[AuthMiddleware::class]);
$router->get('/affectations/creer',             [AffectationController::class, 'creer'],      [AuthMiddleware::class]);
$router->get('/affectations/creer-multiple',     [AffectationController::class, 'creerMultiple'],[AuthMiddleware::class]);
$router->post('/affectations',                  [AffectationController::class, 'enregistrer'],[AuthMiddleware::class]);
$router->post('/affectations/enregistrer-multiple', [AffectationController::class, 'enregistrerMultiple'],[AuthMiddleware::class]);
$router->get('/affectations/{id}/pdf',          [AffectationController::class, 'pdf'],        [AuthMiddleware::class]);
$router->post('/affectations/{id}/notifier',    [AffectationController::class, 'notifier'],   [AuthMiddleware::class]);
$router->get('/affectations/{id}/editer',       [AffectationController::class, 'editer'],     [AuthMiddleware::class]);
$router->post('/affectations/{id}',             [AffectationController::class, 'mettreAJour'],[AuthMiddleware::class]);
$router->post('/affectations/{id}/supprimer',   [AffectationController::class, 'supprimer'],  [AuthMiddleware::class]);

// Utilisateurs (reserve administrateur)
$router->get('/utilisateurs',                   [UtilisateurController::class, 'index'],       [AuthMiddleware::class]);
$router->get('/utilisateurs/creer',             [UtilisateurController::class, 'creer'],       [AuthMiddleware::class]);
$router->post('/utilisateurs',                  [UtilisateurController::class, 'enregistrer'], [AuthMiddleware::class]);
$router->get('/utilisateurs/{id}/editer',       [UtilisateurController::class, 'editer'],      [AuthMiddleware::class]);
$router->post('/utilisateurs/{id}',             [UtilisateurController::class, 'mettreAJour'], [AuthMiddleware::class]);
$router->post('/utilisateurs/{id}/statut',      [UtilisateurController::class, 'changerStatut'],[AuthMiddleware::class]);
$router->post('/utilisateurs/{id}/supprimer',   [UtilisateurController::class, 'supprimer'],   [AuthMiddleware::class]);
