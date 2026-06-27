<?php

namespace App\Models;

use App\Enums\StatutDemandeAdhesion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DemandeAdhesion extends Model
{
    use HasFactory;

    protected $table = 'demandes_adhesion';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'numero_ordre',
        'specialite',
        'annees_experience',
        'structure_sante',
        'ville',
        'chemin_diplome',
        'chemin_justificatif',
        'statut',
        'note_admin',
        'traite_par',
        'traite_le',
    ];

    protected $casts = [
        'statut'            => StatutDemandeAdhesion::class,
        'traite_le'         => 'datetime',
        'annees_experience' => 'integer',
    ];

    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function estEnAttente(): bool
    {
        return $this->statut === StatutDemandeAdhesion::EnAttente;
    }

    // Relations
    public function traitePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    public function gynecologue(): HasOne
    {
        return $this->hasOne(Gynecologue::class, 'demande_adhesion_id');
    }
}
