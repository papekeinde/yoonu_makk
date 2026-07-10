<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ConnexionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifiant' => ['nullable', 'string', 'max:100'],
            'email'       => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('identifiant') && ! $this->filled('email')) {
                $validator->errors()->add('identifiant', 'Veuillez renseigner un email ou un numéro de téléphone.');
            }
        });
    }
}
