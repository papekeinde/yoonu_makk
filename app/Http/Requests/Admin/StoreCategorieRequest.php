<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategorieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'         => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'icone'       => ['nullable', 'string', 'max:50'],
        ];
    }
}
