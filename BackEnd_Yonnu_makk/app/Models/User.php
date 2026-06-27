<?php

namespace App\Models;

use App\Enums\Genre;
use App\Enums\RoleUtilisateur;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'role',
        'genre',
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'date_naissance',
        'ville',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_naissance'    => 'date',
        'password'          => 'hashed',
        'role'              => RoleUtilisateur::class,
        'genre'             => Genre::class,
    ];

    public function isAdmin(): bool
    {
        return $this->role === RoleUtilisateur::Admin;
    }

    public function isPatient(): bool
    {
        return $this->role === RoleUtilisateur::Patient;
    }

    public function estHomme(): bool
    {
        return $this->genre === Genre::Homme;
    }

    public function age(): ?int
    {
        return $this->date_naissance?->age;
    }

    // Relations
    public function femme(): HasOne
    {
        return $this->hasOne(Femme::class);
    }

    public function administrateur(): HasOne
    {
        return $this->hasOne(Administrateur::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function messagesChatbot(): HasMany
    {
        return $this->hasMany(MessageChatbot::class);
    }
}
