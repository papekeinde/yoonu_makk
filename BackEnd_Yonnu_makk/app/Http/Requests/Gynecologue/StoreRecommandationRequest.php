<?php

namespace App\Http\Requests\Gynecologue;

use App\Enums\TypeRecommandation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecommandationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'  => ['required', Rule::enum(TypeRecommandation::class)],
            'titre' => ['required', 'string', 'max:255'],
            'corps' => ['required', 'string'],
        ];
    }
}
