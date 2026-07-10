<?php

namespace App\Models;

use App\Enums\StageMenopause;
use App\Enums\TypeProfilFemme;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Femme extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type_profil',
        'date_debut_menopause',
        'stade_menopause',
        'antecedents_medicaux',
    ];

    protected $casts = [
        'type_profil'          => TypeProfilFemme::class,
        'date_debut_menopause' => 'date',
        'stade_menopause'      => StageMenopause::class,
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function symptomes(): HasMany
    {
        return $this->hasMany(Symptome::class);
    }

    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class);
    }

    public function recommandations(): HasMany
    {
        return $this->hasMany(Recommandation::class);
    }

    public function gynecologues(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Gynecologue::class, 'gynecologue_patientes', 'femme_id', 'gynecologue_id')
                    ->withPivot(['date_prise_en_charge', 'notes'])
                    ->withTimestamps();
    }

    public function grossesses(): HasMany
    {
        return $this->hasMany(Grossesse::class);
    }

    public function grossesseActive(): HasOne
    {
        return $this->hasOne(Grossesse::class)->where('grossesse_active', true)->latestOfMany();
    }

    public function estEnceinte(): bool
    {
        return $this->type_profil === TypeProfilFemme::Grossesse;
    }
}
