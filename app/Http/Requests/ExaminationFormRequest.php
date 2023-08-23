<?php

namespace App\Http\Requests;

use App\Models\Exam;
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
        if($this->isMethod('put')){
            return [
                'title' => ['sometimes', 'string', 'max:255'],
                'description' => ['sometimes', 'string', 'max:5000'],
                'time_limit' => ['sometimes', 'integer', 'min:10'],
                'start_time' => ['sometimes', 'date'],
                'end_time' => ['sometimes', 'date', 'after_or_equal:start_time'],
            ];
        }
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'time_limit' => ['required', 'integer', 'min:10'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after_or_equal:start_time'],
        ];
    }
}
