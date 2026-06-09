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

// ── AUTH (publique) ──
Route::post('/login', [AuthController::class, 'login']);

// ── ROUTES PROTÉGÉES (Sanctum) ──
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Dashboard + Notifications
    Route::get('/dashboard',                                        [DashboardController::class, 'index']);
    Route::get('/dashboard/notifications',                          [DashboardController::class, 'notifications']);
    Route::put('/dashboard/notifications/{notification}/lire',      [DashboardController::class, 'marquerLue']);

    // ── ADMIN uniquement ──
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('specialites', SpecialiteController::class);
    });

    // ── ADMIN + COMMERCIAL ──
    Route::middleware('role:admin,commercial')->group(function () {

        // Clients
        Route::apiResource('clients', ClientController::class);

        // Contrats
        Route::apiResource('contrats', ContratController::class);
        Route::post('contrats/{contrat}/pdf',     [ContratController::class, 'uploadPdf']);
        Route::get('contrats/{contrat}/pdf',      [ContratController::class, 'downloadPdf']);

        // Équipements
        Route::apiResource('equipements', EquipementController::class);

        // Techniciens
        Route::apiResource('techniciens', TechnicienController::class);
    });

    // ── TOUS LES RÔLES (lecture) + ADMIN/COMMERCIAL (écriture) ──
    Route::get('interventions',          [InterventionController::class, 'index']);
    Route::get('interventions/{intervention}', [InterventionController::class, 'show']);
    Route::get('interventions/{intervention}/pdf', [InterventionController::class, 'downloadRapport']);

    Route::middleware('role:admin,commercial')->group(function () {
        Route::post('interventions',                              [InterventionController::class, 'store']);
        Route::put('interventions/{intervention}',                [InterventionController::class, 'update']);
        Route::delete('interventions/{intervention}',             [InterventionController::class, 'destroy']);
        Route::post('interventions/{intervention}/pdf',           [InterventionController::class, 'uploadRapport']);
    });

    // Technicien peut mettre à jour le statut de ses interventions
    Route::middleware('role:technicien')->group(function () {
        Route::put('interventions/{intervention}/statut', [InterventionController::class, 'update']);
    });
});
