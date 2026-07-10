<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Administrateur extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function demandesAdhesionTraitees(): HasMany
    {
        return $this->hasMany(DemandeAdhesion::class, 'traite_par', 'user_id');
    }

    public function contenus(): HasMany
    {
        return $this->hasMany(Contenu::class, 'auteur_id', 'user_id');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class, 'auteur_id', 'user_id');
    }
}
