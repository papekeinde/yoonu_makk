<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'categorie'       => new CategorieContenuResource($this->whenLoaded('categorie')),
            'titre'           => $this->titre,
            'slug'            => $this->slug,
            'description'     => $this->description,
            'url_video'       => $this->url_video,
            'miniature'       => $this->miniature,
            'duree_secondes'  => $this->duree_secondes,
            'langue'          => $this->langue,
            'est_publie'      => $this->est_publie,
            'publie_le'       => $this->publie_le,
            'created_at'      => $this->created_at,
        ];
    }
}
