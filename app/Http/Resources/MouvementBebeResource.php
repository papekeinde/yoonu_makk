<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MouvementBebeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'date_heure'        => $this->date_heure?->format('Y-m-d H:i'),
            'nombre_mouvements' => $this->nombre_mouvements,
            'intensite'         => $this->intensite?->value,
            'intensite_label'   => $this->intensite?->label(),
            'notes'             => $this->notes,
        ];
    }
}
