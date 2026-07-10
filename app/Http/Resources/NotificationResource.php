<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'type'   => $this->type,
            'titre'  => $this->titre,
            'corps'  => $this->corps,
            'donnees' => $this->donnees,
            'est_lu' => $this->est_lu,
            'lu_le'  => $this->lu_le,
            'created_at' => $this->created_at,
        ];
    }
}
