<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Intervention extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'client_id',
        'contrat_id',
        'created_by',
        'type_intervention',
        'priorite',
        'statut',
        'date_planifiee',
        'date_debut_reelle',
        'date_fin_reelle',
        'duree_minutes',
        'cout',
        'pdf_rapport_path',
    ];

    protected $casts = [
        'date_planifiee'    => 'datetime',
        'date_debut_reelle' => 'datetime',
        'date_fin_reelle'   => 'datetime',
        'cout'              => 'decimal:2',
    ];

    // ── Relations ──

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    // Créateur de l'intervention
    public function createur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Techniciens affectés (N-N via intervention_technicien)
    public function techniciens()
    {
        return $this->belongsToMany(User::class, 'intervention_technicien');
    }

    // Notifications liées
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // ── Helpers ──

    // Calcule et enregistre la durée en minutes
    public function calculerDuree(): void
    {
        if ($this->date_debut_reelle && $this->date_fin_reelle) {
            $this->duree_minutes = (int) $this->date_debut_reelle
                ->diffInMinutes($this->date_fin_reelle);
            $this->save();
        }
    }

    // ── Scopes ──
    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopePlanifiees($query)
    {
        return $query->where('statut', 'planifiee');
    }

    public function scopeHautePriorite($query)
    {
        return $query->where('priorite', 'haute');
    }
}
