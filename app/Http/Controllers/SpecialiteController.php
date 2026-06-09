<?php

namespace App\Http\Controllers;

use App\Models\Specialite;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpecialiteController extends Controller
{
    // GET /api/specialites
    public function index()
    {
        $specialites = Specialite::withCount('techniciens')
            ->orderBy('nom')
            ->get();

        return response()->json($specialites);
    }

    // POST /api/specialites
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:100|unique:specialites,nom',
            'description' => 'nullable|string|max:255',
        ]);

        $specialite = Specialite::create($data);

        return response()->json($specialite, 201);
    }

    // GET /api/specialites/{id}
    public function show(Specialite $specialite)
    {
        // Retourne la spécialité avec la liste de ses techniciens
        $specialite->load('techniciens.user:id,nom,prenom,telephone');

        return response()->json($specialite);
    }

    // PUT /api/specialites/{id}
    public function update(Request $request, Specialite $specialite)
    {
        $data = $request->validate([
            'nom'         => ['sometimes', 'string', 'max:100', Rule::unique('specialites')->ignore($specialite->id)],
            'description' => 'nullable|string|max:255',
        ]);

        $specialite->update($data);

        return response()->json($specialite);
    }

    // DELETE /api/specialites/{id}
    public function destroy(Specialite $specialite)
    {
        // Vérifier si des techniciens ont cette spécialité
        if ($specialite->techniciens()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer : des techniciens ont cette spécialité.',
            ], 422);
        }

        $specialite->delete();

        return response()->json(['message' => 'Spécialité supprimée.']);
    }
}
