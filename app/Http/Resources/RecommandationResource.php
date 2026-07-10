<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecommandationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type,
            'titre'       => $this->titre,
            'corps'       => $this->corps,
            'genere_par'  => $this->genere_par,
            'gynecologue' => new GynecologueResource($this->whenLoaded('gynecologue')),
            'est_lu'      => $this->est_lu,
            'created_at'  => $this->created_at,
        ];
    }
}
