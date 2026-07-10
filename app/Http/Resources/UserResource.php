<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Profil femme (chargé via ->load('femme') dans les controllers auth)
        $femme = $this->relationLoaded('femme') ? $this->femme : $this->femme()->first();

        return [
            'id'             => $this->id,
            'role'           => $this->role,
            'genre'          => $this->genre,
            'nom'            => $this->nom,
            'prenom'         => $this->prenom,
            'email'          => $this->email,
            'telephone'      => $this->telephone,
            'date_naissance' => $this->date_naissance?->format('Y-m-d'),
            'age'            => $this->date_naissance?->age,
            'ville'          => $this->ville,
            'avatar'         => $this->avatar,
            'email_verifie'  => $this->hasVerifiedEmail(),
            'created_at'     => $this->created_at,
            // Profil de la femme (grossesse | menopause | null pour homme/admin)
            'type_profil'    => $femme?->type_profil ?? null,
            'femme_id'       => $femme?->id ?? null,
        ];
    }
}
