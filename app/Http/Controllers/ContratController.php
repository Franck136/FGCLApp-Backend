<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ContratController extends Controller
{
    // GET /api/contrats
    public function index(Request $request)
    {
        $query = Contrat::with('client:id,raison_sociale', 'commercial:id,nom,prenom');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('expirant_dans')) {
            $query->expirantDans((int) $request->expirant_dans);
        }

        if ($request->filled('type_contrat')) {
            $query->where('type_contrat', $request->type_contrat);
        }

        $contrats = $query->orderBy('date_fin')->paginate(20);

        return response()->json($contrats);
    }

    // POST /api/contrats
    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'          => 'required|exists:clients,id',
            'commercial_id'      => 'nullable|exists:users,id',
            'reference'          => 'required|string|max:50|unique:contrats',
            'type_contrat'       => 'required|in:maintenance_preventive,maintenance_corrective,infogerance,installation',
            'date_signature'     => 'required|date',
            'date_debut'         => 'required|date',
            'date_fin'           => 'required|date|after:date_debut',
            'renouvellement_auto'=> 'boolean',
            'statut'             => 'sometimes|in:actif,suspendu,expire,resilie',
            'pdf'                => 'nullable|mimes:pdf|max:10240',
        ]);

        // Calcul automatique de la durée en mois
        $data['duree_mois'] = (int) Carbon::parse($data['date_debut'])
            ->diffInMonths(Carbon::parse($data['date_fin']));

        // Upload PDF
        if ($request->hasFile('pdf')) {
            $data['pdf_path'] = $request->file('pdf')
                ->store('contrats/pdf', 'local');
            $data['pdf_version'] = 1;
        }

        unset($data['pdf']);
        $contrat = Contrat::create($data);

        return response()->json($contrat->load('client:id,raison_sociale'), 201);
    }

    // GET /api/contrats/{id}
    public function show(Contrat $contrat)
    {
        $contrat->load([
            'client:id,raison_sociale,ville',
            'commercial:id,nom,prenom',
            'interventions' => fn($q) => $q->latest()->limit(5),
        ]);

        return response()->json($contrat);
    }

    // PUT /api/contrats/{id}
    public function update(Request $request, Contrat $contrat)
    {
        $data = $request->validate([
            'type_contrat'        => 'sometimes|in:maintenance_preventive,maintenance_corrective,infogerance,installation',
            'date_signature'      => 'sometimes|date',
            'date_debut'          => 'sometimes|date',
            'date_fin'            => 'sometimes|date',
            'renouvellement_auto' => 'boolean',
            'statut'              => 'sometimes|in:actif,suspendu,expire,resilie',
            'commercial_id'       => 'nullable|exists:users,id',
        ]);

        // Recalcul durée si les dates changent
        $debut = $data['date_debut'] ?? $contrat->date_debut;
        $fin   = $data['date_fin']   ?? $contrat->date_fin;
        $data['duree_mois'] = (int) Carbon::parse($debut)->diffInMonths(Carbon::parse($fin));

        $contrat->update($data);

        return response()->json($contrat);
    }

    // DELETE /api/contrats/{id}
    public function destroy(Contrat $contrat)
    {
        if ($contrat->pdf_path) {
            Storage::disk('local')->delete($contrat->pdf_path);
        }

        $contrat->delete();

        return response()->json(['message' => 'Contrat supprimé.']);
    }

    // POST /api/contrats/{id}/pdf  — remplacer le PDF
    public function uploadPdf(Request $request, Contrat $contrat)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:10240',
        ]);

        // Supprimer l'ancien PDF
        if ($contrat->pdf_path) {
            Storage::disk('local')->delete($contrat->pdf_path);
        }

        $path = $request->file('pdf')->store('contrats/pdf', 'local');

        $contrat->update([
            'pdf_path'    => $path,
            'pdf_version' => $contrat->pdf_version + 1,
        ]);

        return response()->json([
            'message'     => 'PDF mis à jour.',
            'pdf_version' => $contrat->pdf_version,
        ]);
    }

    // GET /api/contrats/{id}/pdf  — télécharger le PDF
    public function downloadPdf(Contrat $contrat)
    {
        if (! $contrat->pdf_path || ! Storage::disk('local')->exists($contrat->pdf_path)) {
            return response()->json(['message' => 'Aucun PDF disponible.'], 404);
        }

        return Storage::disk('local')->download(
            $contrat->pdf_path,
            'Contrat-' . $contrat->reference . '.pdf'
        );
    }
}
