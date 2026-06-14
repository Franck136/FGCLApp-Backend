<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contrat;
use App\Models\Intervention;
use App\Models\Equipement;
use Illuminate\Support\Facades\Storage;

class ClientEspaceController extends Controller
{
    // Récupère le client lié à l'utilisateur connecté
    private function getClient(Request $request)
    {
        return $request->user()->client;
    }

    // GET /api/client/dashboard
    public function dashboard(Request $request)
    {
        $client = $this->getClient($request);

        if (!$client) {
            return response()->json(['message' => 'Aucun client associé à ce compte.'], 404);
        }

        $contrats      = $client->contrats;
        $interventions = $client->interventions;
        $equipements   = $client->equipements;

        return response()->json([
            'client' => [
                'id'            => $client->id,
                'raison_sociale'=> $client->raison_sociale,
                'secteur'       => $client->secteur_activite,
                'ville'         => $client->ville,
                'statut'        => $client->statut,
            ],
            'stats' => [
                'contrats_total'           => $contrats->count(),
                'contrats_actifs'          => $contrats->where('statut', 'actif')->count(),
                'contrats_expirant'        => $contrats->filter(fn($c) =>
                    $c->statut === 'actif' && $c->joursRestants() <= 30 && $c->joursRestants() >= 0
                )->count(),
                'interventions_total'      => $interventions->count(),
                'interventions_en_cours'   => $interventions->where('statut', 'en_cours')->count(),
                'interventions_planifiees' => $interventions->where('statut', 'planifiee')->count(),
                'interventions_terminees'  => $interventions->where('statut', 'terminee')->count(),
                'equipements_total'        => $equipements->count(),
                'equipements_hors_service' => $equipements->where('etat', 'hors_service')->count(),
            ],
            'contrats_expirants' => $contrats
                ->filter(fn($c) => $c->statut === 'actif' && $c->joursRestants() <= 30 && $c->joursRestants() >= 0)
                ->map(fn($c) => [
                    'id'             => $c->id,
                    'reference'      => $c->reference,
                    'date_fin'       => $c->date_fin->format('d/m/Y'),
                    'jours_restants' => $c->joursRestants(),
                    'type_contrat'   => $c->type_contrat,
                ]),
            'interventions_recentes' => $interventions
                ->sortByDesc('date_planifiee')
                ->take(5)
                ->values()
                ->map(fn($i) => [
                    'id'               => $i->id,
                    'reference'        => $i->reference,
                    'type_intervention'=> $i->type_intervention,
                    'statut'           => $i->statut,
                    'priorite'         => $i->priorite,
                    'date_planifiee'   => $i->date_planifiee,
                ]),
        ]);
    }

    // GET /api/client/contrats
    public function contrats(Request $request)
    {
        $client = $this->getClient($request);

        if (!$client) {
            return response()->json(['message' => 'Aucun client associé.'], 404);
        }

        $contrats = $client->contrats()
            ->orderBy('date_fin')
            ->get();

        return response()->json($contrats);
    }

    // GET /api/client/contrats/{id}/pdf
    public function contratPdf(Request $request, Contrat $contrat)
    {
        $client = $this->getClient($request);

        // Vérifier que ce contrat appartient bien à ce client
        if ($contrat->client_id !== $client->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        if (!$contrat->pdf_path || !Storage::disk('local')->exists($contrat->pdf_path)) {
            return response()->json(['message' => 'Aucun PDF disponible.'], 404);
        }

        return Storage::disk('local')->download(
            $contrat->pdf_path,
            'Contrat-' . $contrat->reference . '.pdf'
        );
    }

    // GET /api/client/interventions
    public function interventions(Request $request)
    {
        $client = $this->getClient($request);

        if (!$client) {
            return response()->json(['message' => 'Aucun client associé.'], 404);
        }

        $statut = $request->statut;

        $query = $client->interventions()
            ->with('techniciens:id,nom,prenom')
            ->orderBy('date_planifiee', 'desc');

        if ($statut) {
            $query->where('statut', $statut);
        }

        return response()->json($query->paginate(20));
    }

    // GET /api/client/equipements
    public function equipements(Request $request)
    {
        $client = $this->getClient($request);

        if (!$client) {
            return response()->json(['message' => 'Aucun client associé.'], 404);
        }

        $etat = $request->etat;
        $type = $request->type_equipement;

        $query = $client->equipements()->orderBy('type_equipement');

        if ($etat) $query->where('etat', $etat);
        if ($type) $query->where('type_equipement', $type);

        return response()->json($query->get());
    }
}
