<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfessionnelSante extends Model
{
    protected $table = 'professionnels_sante';

    protected $fillable = [
        'gynecologue_id','nom','prenom','email','telephone','ville',
        'structure_sante','bio','photo','type_professionnel','specialite',
        'annees_experience','numero_ordre','langues_parles','profils_pris_en_charge',
        'disponible_en_ligne','disponible_en_cabinet','tarif_consultation',
        'jours_disponibles','horaires','adresse','latitude','longitude',
        'nb_avis','note_moyenne','nb_consultations','profil_verifie','actif',
    ];

    protected $casts = [
        'langues_parles'          => 'array',
        'profils_pris_en_charge'  => 'array',
        'jours_disponibles'       => 'array',
        'disponible_en_ligne'     => 'boolean',
        'disponible_en_cabinet'   => 'boolean',
        'profil_verifie'          => 'boolean',
        'actif'                   => 'boolean',
        'tarif_consultation'      => 'decimal:0',
        'note_moyenne'            => 'decimal:2',
    ];

    // ── Relations ──────────────────────────────────────────────────────────

    public function gynecologue(): BelongsTo
    {
        return $this->belongsTo(Gynecologue::class);
    }

    public function demandes(): HasMany
    {
        return $this->hasMany(DemandeIntermediation::class, 'professionnel_id');
    }

    public function avis(): HasMany
    {
        return $this->hasMany(AvisProfessionnel::class, 'professionnel_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActifs($q)           { return $q->where('actif', true); }
    public function scopeVerifies($q)         { return $q->where('profil_verifie', true); }
    public function scopeDisponiblesEnLigne($q){ return $q->where('disponible_en_ligne', true); }

    public function scopePourProfil($q, string $profil)
    {
        return $q->whereJsonContains('profils_pris_en_charge', $profil);
    }

    public function scopeParVille($q, string $ville)
    {
        return $q->where('ville', 'like', "%{$ville}%");
    }

    // ── Accesseurs ─────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function getLabelTypeAttribute(): string
    {
        return match($this->type_professionnel) {
            'gynecologue'          => 'Gynécologue-Obstétricien',
            'sage_femme'           => 'Sage-femme',
            'nutritionniste'       => 'Nutritionniste',
            'psychologue_perinatal'=> 'Psychologue périnatal',
            'kinesitherapeute'     => 'Kinésithérapeute',
            'infirmiere'           => 'Infirmière',
            'medecin_generaliste'  => 'Médecin généraliste',
            'coach_prenatal'       => 'Coach prénatal',
            default                => 'Professionnel de santé',
        };
    }

    /** Recalcule note_moyenne et nb_avis après un nouvel avis */
    public function recalculerNote(): void
    {
        $stats = $this->avis()->where('approuve', true)
                      ->selectRaw('COUNT(*) as cnt, AVG(note) as avg')->first();
        $this->update([
            'nb_avis'       => $stats->cnt ?? 0,
            'note_moyenne'  => round($stats->avg ?? 0, 2),
        ]);
    }
}
