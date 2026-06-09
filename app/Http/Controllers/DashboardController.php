<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contrat;
use App\Models\Equipement;
use App\Models\Intervention;
use App\Models\Notification;
use App\Models\Technicien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // GET /api/dashboard
    public function index()
    {
        return response()->json([
            'stats'              => $this->stats(),
            'interventions_mois' => $this->interventionsParMois(),
            'contrats_expirants' => $this->contratsExpirants(),
            'interventions_recent' => $this->interventionsRecentes(),
        ]);
    }

    // ── Statistiques générales ──
    private function stats(): array
    {
        return [
            'clients_actifs'            => Client::where('statut', 'actif')->count(),
            'contrats_actifs'           => Contrat::where('statut', 'actif')->count(),
            'contrats_expirant_bientot' => Contrat::expirantDans(30)->count(),
            'interventions_en_cours'    => Intervention::where('statut', 'en_cours')->count(),
            'interventions_planifiees'  => Intervention::where('statut', 'planifiee')->count(),
            'techniciens_disponibles'   => Technicien::where('disponible', true)->count(),
            'techniciens_total'         => Technicien::count(),
            'equipements_hors_service'  => Equipement::where('etat', 'hors_service')->count(),
        ];
    }

    // ── Interventions par mois (6 derniers mois) ──
    private function interventionsParMois(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $mois  = Carbon::now()->subMonths($i);
            $count = Intervention::whereYear('created_at', $mois->year)
                                 ->whereMonth('created_at', $mois->month)
                                 ->count();
            $data[] = [
                'mois'  => $mois->translatedFormat('M Y'),
                'total' => $count,
            ];
        }
        return $data;
    }

    // ── Contrats expirant dans 30 jours ──
    private function contratsExpirants(): object
    {
        return Contrat::with('client:id,raison_sociale')
            ->expirantDans(30)
            ->orderBy('date_fin')
            ->get()
            ->map(fn($c) => [
                'id'            => $c->id,
                'reference'     => $c->reference,
                'client'        => $c->client->raison_sociale,
                'date_fin'      => $c->date_fin->format('d/m/Y'),
                'jours_restants'=> $c->joursRestants(),
            ]);
    }

    // ── 5 dernières interventions ──
    private function interventionsRecentes(): object
    {
        return Intervention::with([
            'client:id,raison_sociale',
            'techniciens:id,nom,prenom',
        ])
        ->latest()
        ->limit(5)
        ->get();
    }

    // GET /api/dashboard/notifications
    public function notifications(Request $request)
    {
        $notifs = Notification::where('user_id', $request->user()->id)
            ->nonLues()
            ->latest('created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'notifications' => $notifs,
            'total_non_lues' => $notifs->count(),
        ]);
    }

    // PUT /api/dashboard/notifications/{id}/lire
    public function marquerLue(Request $request, Notification $notification)
    {
        $notification->update(['lu' => true]);

        return response()->json(['message' => 'Notification marquée comme lue.']);
    }
}
