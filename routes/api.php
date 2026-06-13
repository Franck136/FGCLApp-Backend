<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\TechnicienController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SpecialiteController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AlertController;

// ══ AUTH (publique) ══
Route::post('/login', [AuthController::class, 'login']);

// ══ ROUTES PROTÉGÉES ══
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Recherche globale — tous les rôles
    Route::get('/search', [SearchController::class, 'index']);

    // Dashboard — tous les rôles
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Notifications — tous les rôles (chacun voit les siennes)
    Route::get('/notifications',                      [NotificationController::class, 'index']);
    Route::get('/notifications/{notification}',       [NotificationController::class, 'show']);
    Route::put('/notifications/lire-tout',            [NotificationController::class, 'marquerToutesLues']);
    Route::put('/notifications/{notification}/lire',  [NotificationController::class, 'marquerLue']);
    Route::delete('/notifications/{notification}',    [NotificationController::class, 'destroy']);

    // Spécialités — lecture : tous | écriture : admin
    Route::get('/specialites',          [SpecialiteController::class, 'index']);
    Route::get('/specialites/{specialite}', [SpecialiteController::class, 'show']);

    // ══ ADMIN uniquement ══
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('specialites',                    [SpecialiteController::class, 'store']);
        Route::put('specialites/{specialite}',        [SpecialiteController::class, 'update']);
        Route::delete('specialites/{specialite}',     [SpecialiteController::class, 'destroy']);

        // Alertes manuelles
        Route::post('admin/generer-alertes',          [AlertController::class, 'generer']);
    });

    // ══ ADMIN + COMMERCIAL ══
    Route::middleware('role:admin,commercial')->group(function () {

        // Clients
        Route::apiResource('clients', ClientController::class);
        Route::get('clients/{client}/statistiques',   [ClientController::class, 'statistiques']);

        // Contrats
        Route::apiResource('contrats', ContratController::class);
        Route::post('contrats/{contrat}/pdf',         [ContratController::class, 'uploadPdf']);
        Route::get('contrats/{contrat}/pdf',          [ContratController::class, 'downloadPdf']);
        Route::post('contrats/{contrat}/renouveler',  [ContratController::class, 'renouveler']);

        // Équipements
        Route::apiResource('equipements', EquipementController::class);
        Route::get('equipements/{equipement}/interventions', [EquipementController::class, 'interventions']);

        // ══ TECHNICIEN — son propre profil ══
    Route::middleware('role:technicien')->group(function () {
        Route::get('techniciens/MonProfil',          [TechnicienController::class, 'monProfil']);
        Route::put('interventions/{intervention}/statut', [InterventionController::class, 'update']);
    });
    
        // Techniciens
        Route::apiResource('techniciens', TechnicienController::class);
        Route::get('techniciens/disponibles',         [TechnicienController::class, 'disponibles']);
        Route::get('techniciens/{technicien}/interventions', [TechnicienController::class, 'interventions']);
    });


    // ══ INTERVENTIONS ══
    // Lecture : tous les rôles
    Route::get('interventions',                       [InterventionController::class, 'index']);
    Route::get('interventions/{intervention}',        [InterventionController::class, 'show']);
    Route::get('interventions/{intervention}/pdf',    [InterventionController::class, 'downloadRapport']);
    Route::get('interventions/{intervention}/equipements', [InterventionController::class, 'equipements']);

    // Écriture : admin + commercial
    Route::middleware('role:admin,commercial')->group(function () {
        Route::post('interventions',                  [InterventionController::class, 'store']);
        Route::put('interventions/{intervention}',    [InterventionController::class, 'update']);
        Route::delete('interventions/{intervention}', [InterventionController::class, 'destroy']);
        Route::post('interventions/{intervention}/pdf', [InterventionController::class, 'uploadRapport']);
        Route::post('interventions/{intervention}/equipements/{equipement}', [InterventionController::class, 'attachEquipement']);
        Route::delete('interventions/{intervention}/equipements/{equipement}', [InterventionController::class, 'detachEquipement']);
    });

    // ══ RAPPORTS PDF — admin + commercial ══
    Route::middleware('role:admin,commercial')->prefix('rapports')->group(function () {
        Route::get('client/{client}',                 [RapportController::class, 'client']);
        Route::get('intervention/{intervention}',     [RapportController::class, 'intervention']);
        Route::get('technicien/{technicien}',         [RapportController::class, 'technicien']);
        Route::get('contrats',                        [RapportController::class, 'contrats']);
    });
});
