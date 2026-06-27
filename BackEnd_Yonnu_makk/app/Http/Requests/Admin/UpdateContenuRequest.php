<?php

namespace App\Http\Requests\Admin;

use App\Enums\TypeContenu;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categorie_id'    => ['sometimes', 'exists:categories_contenus,id'],
            'type'            => ['sometimes', Rule::enum(TypeContenu::class)],
            'titre'           => ['sometimes', 'string', 'max:255'],
            'corps'           => ['sometimes', 'string'],
            'image_couverture' => ['nullable', 'image', 'max:2048'],
            'langue'          => ['nullable', 'in:fr,wo'],
            'est_publie'      => ['nullable', 'boolean'],
        ];
    }
}
