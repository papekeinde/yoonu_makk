<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SymptomeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'date_journal'  => $this->date_journal->format('Y-m-d'),
            'note_generale' => $this->note_generale,
            'entrees'       => EntreeSymptomeResource::collection($this->whenLoaded('entrees')),
            'created_at'    => $this->created_at,
        ];
    }
}
