<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntreeSymptomeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'type_symptome' => $this->type_symptome,
            'label'         => $this->type_symptome->label(),
            'intensite'     => $this->intensite,
            'commentaire'   => $this->commentaire,
        ];
    }
}
