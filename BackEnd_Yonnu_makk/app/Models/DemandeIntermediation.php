<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeIntermediation extends Model
{
    protected $table = 'demandes_intermediation';

    protected $fillable = [
        'femme_id','professionnel_id','type_demande','message','motif',
        'date_souhaitee','heure_souhaitee','date_confirmee','heure_confirmee',
        'statut','reponse_professionnel','note_professionnel','compte_rendu',
        'urgente','vue_par_professionnel_le','traitee_le',
    ];

    protected $casts = [
        'date_souhaitee'             => 'date',
        'date_confirmee'             => 'date',
        'urgente'                    => 'boolean',
        'vue_par_professionnel_le'   => 'datetime',
        'traitee_le'                 => 'datetime',
    ];

    public function femme(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Femme::class);
    }

    public function professionnel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProfessionnelSante::class, 'professionnel_id');
    }

    public function avis(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AvisProfessionnel::class, 'demande_id');
    }

    public function scopeEnAttente($q)  { return $q->where('statut', 'en_attente'); }
    public function scopeAcceptees($q)  { return $q->where('statut', 'accepte'); }
    public function scopeTerminees($q)  { return $q->where('statut', 'termine'); }

    public function getLabelStatutAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'accepte'    => 'Acceptée',
            'refuse'     => 'Refusée',
            'termine'    => 'Terminée',
            'annule'     => 'Annulée',
            default      => $this->statut,
        };
    }
}
