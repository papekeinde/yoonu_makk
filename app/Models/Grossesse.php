<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grossesse extends Model
{
    use HasFactory;

    protected $fillable = [
        'femme_id',
        'date_debut_grossesse',
        'date_accouchement_prevue',
        'groupe_sanguin',
        'nombre_grossesses_anterieures',
        'nombre_accouchements_anterieurs',
        'antecedents_obstetricaux',
        'grossesse_active',
    ];

    protected $casts = [
        'date_debut_grossesse'     => 'date',
        'date_accouchement_prevue' => 'date',
        'grossesse_active'         => 'boolean',
    ];

    public function femme(): BelongsTo
    {
        return $this->belongsTo(Femme::class);
    }

    public function suivis(): HasMany
    {
        return $this->hasMany(SuiviGrossesse::class);
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementBebe::class);
    }

    public function semainesAmenorrhee(): int
    {
        // Semaines écoulées depuis le début de grossesse (toujours positif).
        // diffInWeeks est signé sous Carbon 3 : on part de la date de début vers maintenant.
        return (int) $this->date_debut_grossesse->diffInWeeks(now());
    }

    public function joursAvantAccouchement(): int
    {
        return (int) now()->diffInDays($this->date_accouchement_prevue, false);
    }
}
