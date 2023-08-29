<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'examination_id' => $this->examination_id,
            'type' => $this->type,
            'description' => $this->description,
            'option' => OptionResource::collection($this->options)
        ];
    }
}
