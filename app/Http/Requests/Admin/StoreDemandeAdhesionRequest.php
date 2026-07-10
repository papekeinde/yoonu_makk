<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDemandeAdhesionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'                  => ['required', 'string', 'max:255'],
            'prenom'               => ['required', 'string', 'max:255'],
            'email'                => ['required', 'email', 'unique:demandes_adhesion,email'],
            'telephone'            => ['required', 'string', 'max:20'],
            'numero_ordre'         => ['required', 'string', 'unique:demandes_adhesion,numero_ordre'],
            'specialite'           => ['required', 'string', 'max:255'],
            'annees_experience'    => ['required', 'integer', 'min:0'],
            'structure_sante'      => ['required', 'string', 'max:255'],
            'ville'                => ['required', 'string', 'max:100'],
            'diplome'              => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'justificatif'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
