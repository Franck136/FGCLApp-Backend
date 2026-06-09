<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technicien extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'zone_intervention',
        'type_contrat_travail',
        'date_embauche',
        'disponible',
        'pdf_contrat_path',
    ];

    protected $casts = [
        'date_embauche' => 'date',
        'disponible'    => 'boolean',
    ];

    // ── Relations ──

    // Compte utilisateur associé (1-1)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Spécialités du technicien (N-N)
    public function specialites()
    {
        return $this->belongsToMany(Specialite::class, 'technicien_specialite');
    }

    // Interventions auxquelles il est affecté (via users)
    public function interventions()
    {
        return $this->user->interventions();
    }

    // ── Scopes ──
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }
}
