<?php

namespace App\Models;

use App\Enums\TypeRecommandation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recommandation extends Model
{
    use HasFactory;

    protected $table = 'recommandations';

    protected $fillable = [
        'femme_id',
        'type',
        'titre',
        'corps',
        'genere_par',
        'gynecologue_id',
        'est_lu',
    ];

    protected $casts = [
        'type'    => TypeRecommandation::class,
        'est_lu'  => 'boolean',
    ];

    // Scopes
    public function scopeNonLu(Builder $query): Builder
    {
        return $query->where('est_lu', false);
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
