<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class AlerteController extends Controller
{
    // POST /api/admin/generer-alertes
    public function generer()
    {
        $generees = 0;

        // Contrats expirant dans 30 jours
        $contrats30 = Contrat::expirantDans(30)
            ->whereDoesntHave('notifications', fn($q) =>
                $q->where('type', 'echeance_contrat')
                  ->whereRaw('DATEDIFF(NOW(), created_at) < 1') // pas déjà envoyée aujourd'hui
            )
            ->get();

        // Contrats expirant dans 7 jours
        $contrats7 = Contrat::expirantDans(7)->get();

        $adminsEtCommerciaux = User::whereIn('role', ['admin', 'commercial'])
            ->where('statut', 'actif')
            ->get();

        foreach ($adminsEtCommerciaux as $user) {
            foreach ($contrats30 as $contrat) {
                $jours = $contrat->joursRestants();

                // Éviter les doublons
                $existe = Notification::where('user_id', $user->id)
                    ->where('contrat_id', $contrat->id)
                    ->where('type', 'echeance_contrat')
                    ->whereRaw('DATE(created_at) = CURDATE()')
                    ->exists();

                if ($existe) continue;

                Notification::create([
                    'user_id'    => $user->id,
                    'type'       => 'echeance_contrat',
                    'titre'      => "Contrat expirant bientôt — J-{$jours}",
                    'message'    => "Le contrat {$contrat->reference} du client {$contrat->client->raison_sociale} expire dans {$jours} jours (le {$contrat->date_fin->format('d/m/Y')}).",
                    'lu'         => false,
                    'contrat_id' => $contrat->id,
                ]);

                $generees++;
            }
        }

        return response()->json([
            'message'              => 'Alertes générées avec succès.',
            'notifications_creees' => $generees,
            'contrats_j30'         => $contrats30->count(),
            'contrats_j7'          => $contrats7->count(),
        ]);
    }
}
