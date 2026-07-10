<?php

namespace App\Http\Requests\Patient;

use App\Enums\TypeSymptome;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSymptomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_journal'              => ['required', 'date'],
            'note_generale'             => ['nullable', 'string'],
            'entrees'                   => ['required', 'array', 'min:1'],
            'entrees.*.type_symptome'   => ['required', Rule::enum(TypeSymptome::class)],
            'entrees.*.intensite'       => ['required', 'integer', 'min:1', 'max:5'],
            'entrees.*.commentaire'     => ['nullable', 'string'],
        ];
    }
}
