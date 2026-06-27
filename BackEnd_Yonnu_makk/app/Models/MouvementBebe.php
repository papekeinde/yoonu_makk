<?php

namespace App\Models;

use App\Enums\IntensiteMouvement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementBebe extends Model
{
    use HasFactory;

    protected $table = 'mouvements_bebe';

    protected $fillable = [
        'grossesse_id',
        'date_heure',
        'nombre_mouvements',
        'intensite',
        'notes',
    ];

    protected $casts = [
        'date_heure' => 'datetime',
        'intensite'  => IntensiteMouvement::class,
    ];

    public function grossesse(): BelongsTo
    {
        return $this->belongsTo(Grossesse::class);
    }
}
