<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contrat;
use App\Models\Intervention;
use App\Models\Technicien;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RapportController extends Controller
{
    // GET /api/rapports/client/{client}
    public function client(Client $client)
    {
        $client->load([
            'contrats',
            'equipements',
            'interventions.techniciens:id,nom,prenom',
        ]);

        $pdf = Pdf::loadView('rapports.client', compact('client'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Rapport-Client-' . $client->raison_sociale . '.pdf');
    }

    // GET /api/rapports/intervention/{intervention}
    public function intervention(Intervention $intervention)
    {
        $intervention->load([
            'client:id,raison_sociale,adresse,ville,telephone',
            'contrat:id,reference,type_contrat',
            'techniciens:id,nom,prenom,telephone',
            'createur:id,nom,prenom',
        ]);

        $pdf = Pdf::loadView('rapports.intervention', compact('intervention'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Rapport-Intervention-' . $intervention->reference . '.pdf');
    }

    // GET /api/rapports/technicien/{technicien}?debut=2026-01-01&fin=2026-06-30
    public function technicien(Request $request, Technicien $technicien)
    {
        $request->validate([
            'debut' => 'required|date',
            'fin'   => 'required|date|after:debut',
        ]);

        $interventions = $technicien->user
            ->interventions()
            ->whereBetween('date_planifiee', [$request->debut, $request->fin])
            ->with('client:id,raison_sociale', 'contrat:id,reference')
            ->get();

        $technicien->load('user:id,nom,prenom', 'specialites:id,nom');

        $pdf = Pdf::loadView('rapports.technicien', compact('technicien', 'interventions', 'request'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Rapport-Technicien-' . $technicien->user->nom . '.pdf');
    }

    // GET /api/rapports/contrats
    public function contrats(Request $request)
    {
        $query = Contrat::with('client:id,raison_sociale')
            ->orderBy('date_fin');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $contrats = $query->get();

        $pdf = Pdf::loadView('rapports.contrats', compact('contrats'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Rapport-Contrats-' . now()->format('Y-m-d') . '.pdf');
    }
}
