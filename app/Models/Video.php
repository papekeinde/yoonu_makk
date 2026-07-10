<?php

namespace App\Models;

use App\Enums\Langue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'categorie_id',
        'auteur_id',
        'titre',
        'slug',
        'description',
        'url_video',
        'miniature',
        'duree_secondes',
        'langue',
        'est_publie',
        'publie_le',
    ];

    protected $casts = [
        'langue'         => Langue::class,
        'est_publie'     => 'boolean',
        'publie_le'      => 'datetime',
        'duree_secondes' => 'integer',
    ];

    // Scopes
    public function scopePublie(Builder $query): Builder
    {
        return $query->where('est_publie', true);
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
