<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

/**
 * @group Authentication
 */
class RegisteredUserController extends Controller
{
    /**
     * create new user
     *
     * @bodyParam name string required user full name. Example: John Doe
     * @bodyParam email string required valid email. Example: user@email.com
     * @bodyParam role string required should be on of student or teacher. Example: student
     * @bodyParam password string required secured password. Example: password
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        if(!$request->user()->hasRole(RoleEnum::ADMIN->value)) abort(403);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'string', 'in:teacher,student', 'exists:roles,name'],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->input('role'));

        event(new Registered($user));

        return response()->noContent();
    }
}
