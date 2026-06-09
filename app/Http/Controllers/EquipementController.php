<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Equipement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipementController extends Controller
{
    // GET /api/equipements
    public function index(Request $request)
    {
        $query = Equipement::with('client:id,raison_sociale');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('etat')) {
            $query->where('etat', $request->etat);
        }

        if ($request->filled('type_equipement')) {
            $query->where('type_equipement', $request->type_equipement);
        }

        return response()->json($query->paginate(20));
    }

    // POST /api/equipements
    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'       => 'required|exists:clients,id',
            'type_equipement' => 'required|in:PC,Serveur,Imprimante,Routeur,Switch,NAS,Autre',
            'marque'          => 'required|string|max:80',
            'modele'          => 'required|string|max:100',
            'numero_serie'    => 'nullable|string|max:100|unique:equipements',
            'etat'            => 'sometimes|in:bon,degrade,hors_service',
            'localisation'    => 'nullable|string|max:150',
            'pdf'             => 'nullable|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('pdf')) {
            $data['pdf_path'] = $request->file('pdf')->store('equipements/fiches', 'local');
        }

        unset($data['pdf']);
        $equipement = Equipement::create($data);

        return response()->json($equipement, 201);
    }

    // GET /api/equipements/{id}
    public function show(Equipement $equipement)
    {
        return response()->json($equipement->load('client:id,raison_sociale'));
    }

    // PUT /api/equipements/{id}
    public function update(Request $request, Equipement $equipement)
    {
        $data = $request->validate([
            'type_equipement'      => 'sometimes|in:PC,Serveur,Imprimante,Routeur,Switch,NAS,Autre',
            'marque'               => 'sometimes|string|max:80',
            'modele'               => 'sometimes|string|max:100',
            'etat'                 => 'sometimes|in:bon,degrade,hors_service',
            'localisation'         => 'nullable|string|max:150',
            'derniere_maintenance' => 'nullable|date',
        ]);

        $equipement->update($data);

        return response()->json($equipement);
    }

    // DELETE /api/equipements/{id}
    public function destroy(Equipement $equipement)
    {
        if ($equipement->pdf_path) {
            Storage::disk('local')->delete($equipement->pdf_path);
        }

        $equipement->delete();

        return response()->json(['message' => 'Équipement supprimé.']);
    }
}
