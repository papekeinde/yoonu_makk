<?php

namespace App\Http\Requests\Admin;

use App\Enums\TypeNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'           => ['required', Rule::enum(TypeNotification::class)],
            'titre'          => ['required', 'string', 'max:255'],
            'corps'          => ['required', 'string'],
            'user_ids'       => ['nullable', 'array'],
            'user_ids.*'     => ['integer', 'exists:users,id'],
            'a_tous'         => ['nullable', 'boolean'],
        ];
    }
}
