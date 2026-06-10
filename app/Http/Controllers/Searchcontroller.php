<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contrat;
use App\Models\Intervention;
use App\Models\Technicien;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    // GET /api/search?q=SOGETRA
    public function index(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $q = $request->q;

        $clients = Client::where('raison_sociale', 'like', "%$q%")
            ->orWhere('ville', 'like', "%$q%")
            ->orWhere('telephone', 'like', "%$q%")
            ->orWhere('nom_responsable', 'like', "%$q%")
            ->select('id', 'raison_sociale', 'ville', 'statut')
            ->limit(5)
            ->get();

        $contrats = Contrat::where('reference', 'like', "%$q%")
            ->orWhere('type_contrat', 'like', "%$q%")
            ->with('client:id,raison_sociale')
            ->select('id', 'reference', 'type_contrat', 'statut', 'date_fin', 'client_id')
            ->limit(5)
            ->get();

        $interventions = Intervention::where('reference', 'like', "%$q%")
            ->orWhere('type_intervention', 'like', "%$q%")
            ->with('client:id,raison_sociale')
            ->select('id', 'reference', 'type_intervention', 'statut', 'date_planifiee', 'client_id')
            ->limit(5)
            ->get();

        $techniciens = Technicien::whereHas('user', fn($query) =>
            $query->where('nom', 'like', "%$q%")
                  ->orWhere('prenom', 'like', "%$q%")
        )
        ->with('user:id,nom,prenom,telephone', 'specialites:id,nom')
        ->limit(5)
        ->get();

        return response()->json([
            'query'         => $q,
            'clients'       => $clients,
            'contrats'      => $contrats,
            'interventions' => $interventions,
            'techniciens'   => $techniciens,
            'total'         => $clients->count() + $contrats->count()
                             + $interventions->count() + $techniciens->count(),
        ]);
    }
}
