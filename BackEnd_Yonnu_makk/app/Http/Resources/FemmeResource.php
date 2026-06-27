<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FemmeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'user'                 => new UserResource($this->whenLoaded('user')),
            'date_debut_menopause' => $this->date_debut_menopause?->format('Y-m-d'),
            'stade_menopause'      => $this->stade_menopause,
            'antecedents_medicaux' => $this->antecedents_medicaux,
            'created_at'           => $this->created_at,
        ];
    }
}
