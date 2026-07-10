<?php

namespace App\Models;

use App\Enums\TypeNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gynecologue_id',
        'type',
        'titre',
        'corps',
        'donnees',
        'est_lu',
        'lu_le',
    ];

    protected $casts = [
        'type'    => TypeNotification::class,
        'donnees' => 'array',
        'est_lu'  => 'boolean',
        'lu_le'   => 'datetime',
    ];

    // Scopes
    public function scopeNonLu(Builder $query): Builder
    {
        return $query->where('est_lu', false);
    }

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gynecologue(): BelongsTo
    {
        return $this->belongsTo(Gynecologue::class);
    }
}
