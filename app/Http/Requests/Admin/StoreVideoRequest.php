<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categorie_id'   => ['required', 'exists:categories_contenus,id'],
            'titre'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'url_video'      => ['required', 'url', 'max:500'],
            'miniature'      => ['nullable', 'image', 'max:2048'],
            'duree_secondes' => ['nullable', 'integer', 'min:0'],
            'langue'         => ['nullable', 'in:fr,wo'],
            'est_publie'     => ['nullable', 'boolean'],
        ];
    }
}
