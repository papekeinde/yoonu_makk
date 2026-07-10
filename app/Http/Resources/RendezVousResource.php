<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RendezVousResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'femme'            => new FemmeResource($this->whenLoaded('femme')),
            'gynecologue'      => new GynecologueResource($this->whenLoaded('gynecologue')),
            'date_souhaitee'   => $this->date_souhaitee->format('Y-m-d'),
            'heure_souhaitee'  => $this->heure_souhaitee,
            'motif'            => $this->motif,
            'statut'           => $this->statut,
            'date_confirmee'   => $this->date_confirmee?->format('Y-m-d'),
            'heure_confirmee'  => $this->heure_confirmee,
            'note_gynecologue' => $this->note_gynecologue,
            'annule_par'       => $this->annule_par,
            'raison_annulation' => $this->raison_annulation,
            'created_at'       => $this->created_at,
        ];
    }
}
