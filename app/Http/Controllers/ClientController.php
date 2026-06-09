<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    // GET /api/clients
    public function index(Request $request)
    {
        $query = Client::with('commercial:id,nom,prenom');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('raison_sociale', 'like', '%' . $request->search . '%')
                  ->orWhere('ville', 'like', '%' . $request->search . '%')
                  ->orWhere('secteur_activite', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('ville')) {
            $query->where('ville', $request->ville);
        }

        $clients = $query->orderBy('raison_sociale')->paginate(20);

        return response()->json($clients);
    }

    // POST /api/clients
    public function store(Request $request)
    {
        $data = $request->validate([
            'raison_sociale'        => 'required|string|max:150',
            'forme_juridique'       => 'nullable|string|max:50',
            'numero_contribuable'   => 'nullable|string|max:50|unique:clients',
            'secteur_activite'      => 'required|string|max:100',
            'adresse'               => 'required|string',
            'ville'                 => 'required|string|max:80',
            'region'                => 'nullable|string|max:80',
            'email'                 => 'nullable|email',
            'telephone'             => 'required|string|max:20',
            'nom_responsable'       => 'required|string|max:150',
            'poste_responsable'     => 'nullable|string|max:100',
            'telephone_responsable' => 'nullable|string|max:20',
            'email_responsable'     => 'nullable|email',
            'nom_contact2'          => 'nullable|string|max:150',
            'telephone_contact2'    => 'nullable|string|max:20',
            'email_contact2'        => 'nullable|email',
            'statut'                => 'sometimes|in:actif,inactif,suspendu',
            'date_debut_relation'   => 'nullable|date',
            'commercial_id'         => 'nullable|exists:users,id',
        ]);

        $client = Client::create($data);

        return response()->json($client->load('commercial:id,nom,prenom'), 201);
    }

    // GET /api/clients/{id}
    public function show(Client $client)
    {
        $client->load([
            'commercial:id,nom,prenom',
            'contrats',
            'equipements',
            'interventions' => fn($q) => $q->latest()->limit(5),
        ]);

        return response()->json($client);
    }

    // PUT /api/clients/{id}
    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'raison_sociale'        => 'sometimes|string|max:150',
            'forme_juridique'       => 'nullable|string|max:50',
            'numero_contribuable'   => ['nullable', 'string', 'max:50', Rule::unique('clients')->ignore($client->id)],
            'secteur_activite'      => 'sometimes|string|max:100',
            'adresse'               => 'sometimes|string',
            'ville'                 => 'sometimes|string|max:80',
            'region'                => 'nullable|string|max:80',
            'email'                 => 'nullable|email',
            'telephone'             => 'sometimes|string|max:20',
            'nom_responsable'       => 'sometimes|string|max:150',
            'poste_responsable'     => 'nullable|string|max:100',
            'telephone_responsable' => 'nullable|string|max:20',
            'email_responsable'     => 'nullable|email',
            'nom_contact2'          => 'nullable|string|max:150',
            'telephone_contact2'    => 'nullable|string|max:20',
            'email_contact2'        => 'nullable|email',
            'statut'                => 'sometimes|in:actif,inactif,suspendu',
            'date_debut_relation'   => 'nullable|date',
            'commercial_id'         => 'nullable|exists:users,id',
        ]);

        $client->update($data);

        return response()->json($client->load('commercial:id,nom,prenom'));
    }

    // DELETE /api/clients/{id}
    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json(['message' => 'Client supprimé.']);
    }
}
