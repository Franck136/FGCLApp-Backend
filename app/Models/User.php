<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // ── Champs autorisés à la création/modification ──
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'telephone',
        'photo',
        'statut',
    ];

    // ── Champs cachés dans les réponses JSON ──
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ── Conversions automatiques ──
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Helpers rôles ──
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCommercial(): bool
    {
        return $this->role === 'commercial';
    }

    public function isTechnicien(): bool
    {
        return $this->role === 'technicien';
    }

    // ── Relations ──

    public function technicien()
    {
        return $this->hasOne(Technicien::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'commercial_id');
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'commercial_id');
    }

    public function interventionsCreees()
    {
        return $this->hasMany(Intervention::class, 'created_by');
    }

    public function interventions()
    {
        return $this->belongsToMany(Intervention::class, 'intervention_technicien');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function client() 
    {
     return $this->belongsTo(Client::class);
    }
}
