<?php

namespace App\Models;

use App\Enums\StatutRendezVous;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'femme_id',
        'gynecologue_id',
        'date_souhaitee',
        'heure_souhaitee',
        'motif',
        'statut',
        'date_confirmee',
        'heure_confirmee',
        'note_gynecologue',
        'annule_par',
        'raison_annulation',
    ];

    protected $casts = [
        'statut'          => StatutRendezVous::class,
        'date_souhaitee'  => 'date',
        'date_confirmee'  => 'date',
    ];

    // Scopes
    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut', StatutRendezVous::EnAttente->value);
    }

    public function scopeAccepte(Builder $query): Builder
    {
        return $query->where('statut', StatutRendezVous::Accepte->value);
    }

    // Relations
    public function femme(): BelongsTo
    {
        return $this->belongsTo(Femme::class);
    }

    public function gynecologue(): BelongsTo
    {
        return $this->belongsTo(Gynecologue::class);
    }
}
