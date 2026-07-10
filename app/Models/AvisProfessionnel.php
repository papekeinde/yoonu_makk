<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvisProfessionnel extends Model
{
    protected $table = 'avis_professionnels';

    protected $fillable = [
        'femme_id','professionnel_id','demande_id',
        'note','commentaire','recommande','approuve',
    ];

    protected $casts = [
        'recommande' => 'boolean',
        'approuve'   => 'boolean',
        'note'       => 'integer',
    ];

    public function femme(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Femme::class);
    }

    public function professionnel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProfessionnelSante::class, 'professionnel_id');
    }

    public function demande(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DemandeIntermediation::class, 'demande_id');
    }

    /** Après sauvegarde, recalcule la note du professionnel */
    protected static function booted(): void
    {
        static::saved(function (self $avis) {
            $avis->professionnel->recalculerNote();
        });
    }
}
