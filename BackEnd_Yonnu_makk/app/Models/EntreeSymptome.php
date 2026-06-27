<?php

namespace App\Models;

use App\Enums\TypeSymptome;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntreeSymptome extends Model
{
    use HasFactory;

    protected $table = 'entrees_symptomes';

    protected $fillable = [
        'symptome_id',
        'type_symptome',
        'intensite',
        'commentaire',
    ];

    protected $casts = [
        'type_symptome' => TypeSymptome::class,
        'intensite'     => 'integer',
    ];

    // Relations
    public function symptome(): BelongsTo
    {
        return $this->belongsTo(Symptome::class);
    }
}
