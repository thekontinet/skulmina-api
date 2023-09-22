<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Rules\HasRole;
use Illuminate\Foundation\Http\FormRequest;

class CourseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(RoleEnum::ADMIN->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'teacher_id' => ['nullable', 'exists:users,id', new HasRole(RoleEnum::TEACHER)],
            'title' => ['required', 'max:255'],
            'code' => ['required', 'unique:courses,code'],
        ];
    }
}
