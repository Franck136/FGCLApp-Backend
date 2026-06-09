<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipement extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'type_equipement',
        'marque',
        'modele',
        'numero_serie',
        'etat',
        'localisation',
        'derniere_maintenance',
        'pdf_path',
    ];

    protected $casts = [
        'derniere_maintenance' => 'date',
    ];

    // ── Relations ──

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // ── Scopes ──
    public function scopeEnBon($query)
    {
        return $query->where('etat', 'bon');
    }

    public function scopeHorsService($query)
    {
        return $query->where('etat', 'hors_service');
    }
}
