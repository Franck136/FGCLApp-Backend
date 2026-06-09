<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'raison_sociale',
        'forme_juridique',
        'numero_contribuable',
        'secteur_activite',
        'adresse',
        'ville',
        'region',
        'email',
        'telephone',
        'nom_responsable',
        'poste_responsable',
        'telephone_responsable',
        'email_responsable',
        'nom_contact2',
        'telephone_contact2',
        'email_contact2',
        'statut',
        'date_debut_relation',
        'commercial_id',
    ];

    protected $casts = [
        'date_debut_relation' => 'date',
    ];

    // ── Relations ──

    // Commercial FGCL assigné
    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    // Contrats du client
    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    // Équipements du client
    public function equipements()
    {
        return $this->hasMany(Equipement::class);
    }

    // Interventions du client
    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }

    // ── Scopes ──
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }
}
