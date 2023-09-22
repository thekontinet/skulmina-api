<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExaminationFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $data = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'time_limit' => ['required', 'integer', 'min:10'],
            'published_at' => ['nullable', 'date'],
            'question_ids' => ['sometimes', 'array'],
            'question_ids.*' => ['required', 'exists:questions,id'],
        ];

        if ($this->route()->getName() === 'examinations.store') {
            $data['course_id'] = ['required', 'exists:courses,id'];
        }

        return $data;
    }
}
