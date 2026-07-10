<?php

namespace App\Http\Requests\Auth;

use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'            => ['required', 'string', 'max:255'],
            'prenom'         => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'telephone'      => ['nullable', 'string', 'max:20', Rule::unique('users', 'telephone')],
            // Date de naissance obligatoire : permet de connaître l'âge de la personne
            'date_naissance' => ['required', 'date', 'before:today', 'after:1920-01-01'],
            'ville'          => ['nullable', 'string', 'max:100'],
            // Les hommes peuvent s'inscrire pour s'informer
            'genre'          => ['required', 'string', 'in:femme,homme'],
            // Profil santé requis uniquement pour les femmes
            'type_profil'    => ['required_if:genre,femme', 'nullable', 'string', 'in:menopause,grossesse'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_naissance.required' => 'La date de naissance est obligatoire.',
            'date_naissance.before'   => 'La date de naissance doit être dans le passé.',
            'genre.required'          => 'Veuillez indiquer votre genre.',
            'type_profil.required_if' => 'Veuillez choisir votre profil (grossesse ou ménopause).',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('telephone')) {
            return;
        }

        $this->merge([
            'telephone' => PhoneNumber::normalize($this->input('telephone')),
        ]);
    }
}
