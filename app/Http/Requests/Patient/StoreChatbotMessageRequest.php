<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatbotMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message'    => ['required', 'string', 'max:2000'],
            'session_id' => ['nullable', 'uuid'],
            'langue'     => ['nullable', 'in:fr,wo'],
        ];
    }
}
