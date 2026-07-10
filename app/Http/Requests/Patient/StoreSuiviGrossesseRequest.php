<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuiviGrossesseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'semaines_amenorrhee' => ['required', 'integer', 'min:1', 'max:42'],
            'poids_kg'            => ['nullable', 'numeric', 'min:30', 'max:200'],
            'tension_systolique'  => ['nullable', 'integer', 'min:60', 'max:250'],
            'tension_diastolique' => ['nullable', 'integer', 'min:40', 'max:150'],
            'glycemie'            => ['nullable', 'numeric', 'min:0', 'max:30'],
            'notes'               => ['nullable', 'string', 'max:2000'],
            'date_saisie'         => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
