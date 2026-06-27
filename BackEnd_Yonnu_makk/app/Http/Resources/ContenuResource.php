<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'categorie'       => new CategorieContenuResource($this->whenLoaded('categorie')),
            'type'            => $this->type,
            'titre'           => $this->titre,
            'slug'            => $this->slug,
            'corps'           => $this->corps,
            'image_couverture' => $this->image_couverture,
            'langue'          => $this->langue,
            'est_publie'      => $this->est_publie,
            'publie_le'       => $this->publie_le,
            'created_at'      => $this->created_at,
        ];
    }
}
