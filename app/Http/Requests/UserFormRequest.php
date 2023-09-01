<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserFormRequest extends FormRequest
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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'string', 'in:teacher,student', 'exists:roles,name'],
            'password' => ['required', Rules\Password::defaults()],
        ];

        if($this->isMethod('put')){
            $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(auth()->id(), 'id')];
            $rules['password'] = ['sometimes', 'required', Rules\Password::defaults()];
        }

        return $rules;
    }
}
