<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
use App\Models\Intervention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class InterventionController extends Controller
{
    // GET /api/interventions
    public function index(Request $request)
    {
        $query = Intervention::with([
            'client:id,raison_sociale',
            'contrat:id,reference',
            'techniciens:id,nom,prenom',
            'createur:id,nom,prenom',
        ]);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('technicien_id')) {
            $query->whereHas('techniciens', fn($q) =>
                $q->where('users.id', $request->technicien_id)
            );
        }

        $interventions = $query->orderBy('date_planifiee')->paginate(20);

        return response()->json($interventions);
    }

    // POST /api/interventions
    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'         => 'required|exists:clients,id',
            'contrat_id'        => 'nullable|exists:contrats,id',
            'type_intervention' => 'required|in:preventive,corrective,installation,audit',
            'priorite'          => 'required|in:haute,normale,basse',
            'date_planifiee'    => 'required|date',
            'techniciens'       => 'nullable|array',
            'techniciens.*'     => 'exists:users,id',
        ]);

        // Référence automatique : INT-2026-XXX
        $count = Intervention::whereYear('created_at', now()->year)->count() + 1;
        $data['reference']  = 'INT-' . now()->year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $data['created_by'] = $request->user()->id;

        $techniciens = $data['techniciens'] ?? [];
        unset($data['techniciens']);

        $intervention = Intervention::create($data);

        // Affecter les techniciens
        if (! empty($techniciens)) {
            $intervention->techniciens()->attach($techniciens);
        }

        return response()->json(
            $intervention->load('client:id,raison_sociale', 'techniciens:id,nom,prenom'),
            201
        );
    }

    // GET /api/interventions/{id}
    public function show(Intervention $intervention)
    {
        $intervention->load([
            'client:id,raison_sociale,ville,telephone',
            'contrat:id,reference,type_contrat',
            'techniciens:id,nom,prenom,telephone',
            'createur:id,nom,prenom',
        ]);

        return response()->json($intervention);
    }

    // PUT /api/interventions/{id}
    public function update(Request $request, Intervention $intervention)
    {
        $data = $request->validate([
            'statut'            => 'sometimes|in:planifiee,en_cours,terminee,annulee',
            'priorite'          => 'sometimes|in:haute,normale,basse',
            'date_planifiee'    => 'sometimes|date',
            'date_debut_reelle' => 'nullable|date',
            'date_fin_reelle'   => 'nullable|date',
            'cout'              => 'nullable|numeric|min:0',
            'techniciens'       => 'nullable|array',
            'techniciens.*'     => 'exists:users,id',
        ]);

        // Calcul automatique de la durée
        if (isset($data['date_debut_reelle']) && isset($data['date_fin_reelle'])) {
            $data['duree_minutes'] = (int) Carbon::parse($data['date_debut_reelle'])
                ->diffInMinutes(Carbon::parse($data['date_fin_reelle']));
        }

        // Mise à jour des techniciens
        if (isset($data['techniciens'])) {
            $intervention->techniciens()->sync($data['techniciens']);
            unset($data['techniciens']);
        }

        $intervention->update($data);

        return response()->json($intervention->load('techniciens:id,nom,prenom'));
    }

    // DELETE /api/interventions/{id}
    public function destroy(Intervention $intervention)
    {
        if ($intervention->pdf_rapport_path) {
            Storage::disk('local')->delete($intervention->pdf_rapport_path);
        }

        $intervention->delete();

        return response()->json(['message' => 'Intervention supprimée.']);
    }

    // POST /api/interventions/{id}/pdf  — uploader le rapport PDF
    public function uploadRapport(Request $request, Intervention $intervention)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:10240',
        ]);

        if ($intervention->pdf_rapport_path) {
            Storage::disk('local')->delete($intervention->pdf_rapport_path);
        }

        $path = $request->file('pdf')->store('interventions/rapports', 'local');

        $intervention->update(['pdf_rapport_path' => $path]);

        return response()->json(['message' => 'Rapport PDF uploadé.']);
    }

    // GET /api/interventions/{id}/pdf
    public function downloadRapport(Intervention $intervention)
    {
        if (! $intervention->pdf_rapport_path ||
            ! Storage::disk('local')->exists($intervention->pdf_rapport_path)) {
            return response()->json(['message' => 'Aucun rapport disponible.'], 404);
        }

        return Storage::disk('local')->download(
            $intervention->pdf_rapport_path,
            'Rapport-' . $intervention->reference . '.pdf'
        );
    }
}
