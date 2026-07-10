<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class StoreRendezVousRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gynecologue_id'  => ['required', 'exists:gynecologues,id'],
            'date_souhaitee'  => ['required', 'date', 'after:today'],
            'heure_souhaitee' => ['nullable', 'date_format:H:i'],
            'motif'           => ['nullable', 'string', 'max:1000'],
        ];
    }
}
