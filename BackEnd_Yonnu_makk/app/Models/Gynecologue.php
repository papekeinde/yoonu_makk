<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Gynecologue extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'numero_ordre',
        'specialite',
        'annees_experience',
        'structure_sante',
        'ville',
        'tarif_consultation',
        'bio',
        'avatar',
        'demande_adhesion_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'          => 'boolean',
        'annees_experience'  => 'integer',
        'tarif_consultation' => 'integer',
    ];

    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    // Relations
    public function demandeAdhesion(): BelongsTo
    {
        return $this->belongsTo(DemandeAdhesion::class);
    }

    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class, 'gynecologue_id');
    }

    public function recommandations(): HasMany
    {
        return $this->hasMany(Recommandation::class, 'gynecologue_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'gynecologue_id');
    }
}
