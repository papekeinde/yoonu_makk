<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Symptome extends Model
{
    use HasFactory;

    protected $fillable = [
        'femme_id',
        'date_journal',
        'note_generale',
    ];

    protected $casts = [
        'date_journal' => 'date',
    ];

    // Relations
    public function femme(): BelongsTo
    {
        return $this->belongsTo(Femme::class);
    }

    public function entrees(): HasMany
    {
        return $this->hasMany(EntreeSymptome::class);
    }
}
