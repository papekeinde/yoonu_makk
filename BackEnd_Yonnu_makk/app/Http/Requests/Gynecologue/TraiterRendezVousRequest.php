<?php

namespace App\Http\Requests\Gynecologue;

use Illuminate\Foundation\Http\FormRequest;

class TraiterRendezVousRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note_gynecologue' => ['nullable', 'string', 'max:2000'],
            'date_confirmee'   => ['nullable', 'date', 'after:today'],
            'heure_confirmee'  => ['nullable', 'date_format:H:i'],
        ];
    }
}
