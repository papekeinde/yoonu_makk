<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GynecologueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'nom'               => $this->nom,
            'prenom'            => $this->prenom,
            'nom_complet'       => $this->nom_complet,
            'email'             => $this->when($this->shouldShowPrivateInfo($request), $this->email),
            'telephone'         => $this->when($this->shouldShowPrivateInfo($request), $this->telephone),
            'numero_ordre'      => $this->numero_ordre,
            'specialite'        => $this->specialite,
            'annees_experience' => $this->annees_experience,
            'structure_sante'   => $this->structure_sante,
            'ville'             => $this->ville,
            'tarif_consultation' => $this->tarif_consultation,
            'bio'               => $this->bio,
            'avatar'            => $this->avatar,
            'is_active'         => $this->when($this->shouldShowPrivateInfo($request), $this->is_active),
            'created_at'        => $this->created_at,
        ];
    }

    private function shouldShowPrivateInfo(Request $request): bool
    {
        $user = $request->user() ?? $request->user('gynecologue');
        if (! $user) {
            return false;
        }
        if ($user instanceof \App\Models\Gynecologue && $user->id === $this->id) {
            return true;
        }
        if ($user instanceof \App\Models\User && $user->isAdmin()) {
            return true;
        }
        return false;
    }
}
