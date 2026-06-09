<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Contrat extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'commercial_id',
        'reference',
        'type_contrat',
        'date_signature',
        'date_debut',
        'date_fin',
        'duree_mois',
        'renouvellement_auto',
        'statut',
        'pdf_path',
        'pdf_version',
    ];

    protected $casts = [
        'date_signature'     => 'date',
        'date_debut'         => 'date',
        'date_fin'           => 'date',
        'renouvellement_auto' => 'boolean',
    ];

    // ── Relations ──

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // ── Helpers ──

    // Calcule et retourne les jours restants avant expiration
    public function joursRestants(): int
    {
        return (int) Carbon::now()->diffInDays($this->date_fin, false);
    }

    // Vérifie si le contrat expire bientôt (dans N jours)
    public function expireBientot(int $jours = 30): bool
    {
        $restants = $this->joursRestants();
        return $restants >= 0 && $restants <= $jours;
    }

    // ── Scopes ──
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeExpirantDans($query, int $jours)
    {
        return $query->where('statut', 'actif')
                     ->whereBetween('date_fin', [
                         Carbon::now(),
                         Carbon::now()->addDays($jours),
                     ]);
    }
}
