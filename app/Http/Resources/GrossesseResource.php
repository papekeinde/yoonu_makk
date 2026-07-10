<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GrossesseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                              => $this->id,
            'date_debut_grossesse'            => $this->date_debut_grossesse?->format('Y-m-d'),
            'date_accouchement_prevue'        => $this->date_accouchement_prevue?->format('Y-m-d'),
            'groupe_sanguin'                  => $this->groupe_sanguin,
            'nombre_grossesses_anterieures'   => $this->nombre_grossesses_anterieures,
            'nombre_accouchements_anterieurs' => $this->nombre_accouchements_anterieurs,
            'antecedents_obstetricaux'        => $this->antecedents_obstetricaux,
            'grossesse_active'                => $this->grossesse_active,
            'semaines_amenorrhee_actuelles'   => $this->semainesAmenorrhee(),
            'jours_avant_accouchement'        => $this->joursAvantAccouchement(),
            'created_at'                      => $this->created_at?->format('Y-m-d'),
        ];
    }
}
