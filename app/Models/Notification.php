<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public $timestamps = false; // seulement created_at défini en migration

    protected $fillable = [
        'user_id',
        'type',
        'titre',
        'message',
        'lu',
        'contrat_id',
        'intervention_id',
    ];

    protected $casts = [
        'lu'         => 'boolean',
        'created_at' => 'datetime',
    ];

    // ── Relations ──

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function intervention()
    {
        return $this->belongsTo(Intervention::class);
    }

    // ── Scopes ──
    public function scopeNonLues($query)
    {
        return $query->where('lu', false);
    }

    public function scopeEcheances($query)
    {
        return $query->where('type', 'echeance_contrat');
    }
}
