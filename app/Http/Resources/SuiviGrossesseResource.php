<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuiviGrossesseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'semaines_amenorrhee'  => $this->semaines_amenorrhee,
            'poids_kg'             => $this->poids_kg,
            'tension_systolique'   => $this->tension_systolique,
            'tension_diastolique'  => $this->tension_diastolique,
            'glycemie'             => $this->glycemie,
            'notes'                => $this->notes,
            'date_saisie'          => $this->date_saisie?->format('Y-m-d'),
        ];
    }
}
