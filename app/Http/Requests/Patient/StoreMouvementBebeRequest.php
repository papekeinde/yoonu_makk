<?php

namespace App\Http\Requests\Patient;

use App\Enums\IntensiteMouvement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMouvementBebeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_heure'        => ['required', 'date', 'before_or_equal:now'],
            'nombre_mouvements' => ['required', 'integer', 'min:1', 'max:100'],
            'intensite'         => ['required', new Enum(IntensiteMouvement::class)],
            'notes'             => ['nullable', 'string', 'max:500'],
        ];
    }
}
