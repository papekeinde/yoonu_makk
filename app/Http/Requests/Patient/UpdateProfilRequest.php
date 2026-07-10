<?php

namespace App\Http\Requests\Patient;

use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'                  => ['sometimes', 'string', 'max:255'],
            'prenom'               => ['sometimes', 'string', 'max:255'],
            'telephone'            => ['nullable', 'string', 'max:20', Rule::unique('users', 'telephone')->ignore($this->user()?->id)],
            'date_naissance'       => ['nullable', 'date', 'before:today'],
            'ville'                => ['nullable', 'string', 'max:100'],
            'date_debut_menopause' => ['nullable', 'date'],
            'stade_menopause'      => ['nullable', 'in:perimenopause,menopause,postmenopause'],
            'antecedents_medicaux' => ['nullable', 'string'],
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
