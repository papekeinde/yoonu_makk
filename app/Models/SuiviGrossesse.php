<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuiviGrossesse extends Model
{
    use HasFactory;

    protected $table = 'suivis_grossesse';

    protected $fillable = [
        'grossesse_id',
        'semaines_amenorrhee',
        'poids_kg',
        'tension_systolique',
        'tension_diastolique',
        'glycemie',
        'notes',
        'date_saisie',
    ];

    protected $casts = [
        'date_saisie' => 'date',
        'poids_kg'    => 'decimal:2',
        'glycemie'    => 'decimal:2',
    ];

    public function grossesse(): BelongsTo
    {
        return $this->belongsTo(Grossesse::class);
    }
}
