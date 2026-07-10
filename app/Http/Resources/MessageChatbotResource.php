<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageChatbotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'session_id' => $this->session_id,
            'role'       => $this->role,
            'message'    => $this->message,
            'langue'     => $this->langue,
            'created_at' => $this->created_at,
        ];
    }
}
