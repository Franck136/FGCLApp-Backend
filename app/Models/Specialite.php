<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialite extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
    ];

    // ── Relations ──

    // Techniciens ayant cette spécialité (N-N)
    public function techniciens()
    {
        return $this->belongsToMany(Technicien::class, 'technicien_specialite');
    }
}
