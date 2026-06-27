<?php

namespace App\Http\Requests\Gynecologue;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilGynecologueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'telephone'        => ['sometimes', 'string', 'max:20'],
            'specialite'       => ['sometimes', 'string', 'max:255'],
            'structure_sante'  => ['sometimes', 'string', 'max:255'],
            'ville'            => ['sometimes', 'string', 'max:100'],
            'tarif_consultation' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'bio'              => ['nullable', 'string', 'max:2000'],
        ];
    }
}
