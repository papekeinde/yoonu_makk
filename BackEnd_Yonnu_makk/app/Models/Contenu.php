<?php

namespace App\Models;

use App\Enums\Langue;
use App\Enums\TypeContenu;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contenu extends Model
{
    use HasFactory;

    protected $table = 'contenus';

    protected $fillable = [
        'categorie_id',
        'auteur_id',
        'type',
        'titre',
        'slug',
        'corps',
        'image_couverture',
        'langue',
        'est_publie',
        'publie_le',
    ];

    protected $casts = [
        'type'      => TypeContenu::class,
        'langue'    => Langue::class,
        'est_publie' => 'boolean',
        'publie_le' => 'datetime',
    ];

    // Scopes
    public function scopePublie(Builder $query): Builder
    {
        return $query->where('est_publie', true);
    }

    public function scopeParLangue(Builder $query, Langue $langue): Builder
    {
        return $query->where('langue', $langue->value);
    }

    // Relations
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(CategorieContenu::class, 'categorie_id');
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }
}
