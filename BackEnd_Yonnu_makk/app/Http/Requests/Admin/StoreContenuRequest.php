<?php

namespace App\Http\Requests\Admin;

use App\Enums\TypeContenu;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categorie_id'    => ['required', 'exists:categories_contenus,id'],
            'type'            => ['required', Rule::enum(TypeContenu::class)],
            'titre'           => ['required', 'string', 'max:255'],
            'corps'           => ['required', 'string'],
            'image_couverture' => ['nullable', 'image', 'max:2048'],
            'langue'          => ['nullable', 'in:fr,wo'],
            'est_publie'      => ['nullable', 'boolean'],
        ];
    }
}
