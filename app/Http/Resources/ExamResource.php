<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
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
            'user_id' => $this->user_id,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'time_limit' => $this->time_limit,
            'published_at' => $this->published_at,
            'questions' => QuestionResource::collection($this->questions),
        ];
    }
}
