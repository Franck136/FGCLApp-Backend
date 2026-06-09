<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Technicien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TechnicienController extends Controller
{
    // GET /api/techniciens
    public function index(Request $request)
    {
        $query = Technicien::with([
            'user:id,nom,prenom,email,telephone,photo',
            'specialites:id,nom',
        ]);

        if ($request->filled('disponible')) {
            $query->where('disponible', $request->boolean('disponible'));
        }

        if ($request->filled('specialite_id')) {
            $query->whereHas('specialites', fn($q) =>
                $q->where('specialites.id', $request->specialite_id)
            );
        }

        if ($request->filled('zone')) {
            $query->where('zone_intervention', 'like', '%' . $request->zone . '%');
        }

        return response()->json($query->paginate(20));
    }

    // POST /api/techniciens
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'              => 'required|exists:users,id|unique:techniciens,user_id',
            'zone_intervention'    => 'required|string|max:150',
            'type_contrat_travail' => 'required|in:CDI,CDD,stagiaire,prestataire',
            'date_embauche'        => 'nullable|date',
            'disponible'           => 'boolean',
            'specialites'          => 'nullable|array',
            'specialites.*'        => 'exists:specialites,id',
            'pdf'                  => 'nullable|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('pdf')) {
            $data['pdf_contrat_path'] = $request->file('pdf')
                ->store('techniciens/contrats', 'local');
        }

        $specialites = $data['specialites'] ?? [];
        unset($data['specialites'], $data['pdf']);

        $technicien = Technicien::create($data);

        if (! empty($specialites)) {
            $technicien->specialites()->attach($specialites);
        }

        return response()->json(
            $technicien->load('user:id,nom,prenom', 'specialites:id,nom'),
            201
        );
    }

    // GET /api/techniciens/{id}
    public function show(Technicien $technicien)
    {
        $technicien->load([
            'user:id,nom,prenom,email,telephone,photo',
            'specialites:id,nom',
        ]);

        return response()->json($technicien);
    }

    // PUT /api/techniciens/{id}
    public function update(Request $request, Technicien $technicien)
    {
        $data = $request->validate([
            'zone_intervention'    => 'sometimes|string|max:150',
            'type_contrat_travail' => 'sometimes|in:CDI,CDD,stagiaire,prestataire',
            'date_embauche'        => 'nullable|date',
            'disponible'           => 'boolean',
            'specialites'          => 'nullable|array',
            'specialites.*'        => 'exists:specialites,id',
        ]);

        if (isset($data['specialites'])) {
            $technicien->specialites()->sync($data['specialites']);
            unset($data['specialites']);
        }

        $technicien->update($data);

        return response()->json($technicien->load('specialites:id,nom'));
    }

    // DELETE /api/techniciens/{id}
    public function destroy(Technicien $technicien)
    {
        if ($technicien->pdf_contrat_path) {
            Storage::disk('local')->delete($technicien->pdf_contrat_path);
        }

        $technicien->delete();

        return response()->json(['message' => 'Fiche technicien supprimée.']);
    }
}
