<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemandeAdhesionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'nom_complet'       => $this->nom_complet,
            'nom'               => $this->nom,
            'prenom'            => $this->prenom,
            'email'             => $this->email,
            'telephone'         => $this->telephone,
            'numero_ordre'      => $this->numero_ordre,
            'specialite'        => $this->specialite,
            'annees_experience' => $this->annees_experience,
            'structure_sante'   => $this->structure_sante,
            'ville'             => $this->ville,
            'chemin_diplome'    => $this->chemin_diplome,
            'chemin_justificatif' => $this->chemin_justificatif,
            'statut'            => $this->statut,
            'note_admin'        => $this->note_admin,
            'traite_le'         => $this->traite_le,
            'created_at'        => $this->created_at,
        ];
    }
}
