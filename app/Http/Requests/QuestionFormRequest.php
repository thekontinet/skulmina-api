<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionFormRequest extends FormRequest
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
        return [
            'description' => ['required'],
            'options' => ['required', 'array'],
            'answers' => ['required', 'array']
        ];
    }

    public function getFormattedOptions()
    {
        $optionsArr = [];

        foreach($this->input('options') as $option){
            $optionsArr[] = ['value' => $option, 'is_correct' => false];
        }

        foreach($this->input('answers') as $option){
            $optionsArr[] = ['value' => $option, 'is_correct' => true];
        }

        return $optionsArr;
    }
}
