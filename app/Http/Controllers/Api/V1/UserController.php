<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserFormRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Manage User
 */
class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * List users
     */
    public function index()
    {
        return UserResource::collection(User::paginate());
    }

    /**
     * Create new user
     *
     * @bodyParam name string required user full name. Example: John Doe
     * @bodyParam email string required valid email. Example: user@email.com
     * @bodyParam role string required should be on of student or teacher. Example: student
     * @bodyParam password string required secured password. Example: password
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(UserFormRequest $request): Response
    {
        $request->validated();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->input('role'));

        event(new Registered($user));

        return response()->noContent();
    }

    /**
     * Get single user
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update user record
     */
    public function update(UserFormRequest $request, User $user)
    {
        $request->validated();

        $user->fill([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ]);

        if($request->input('password')){
            $user->fill([
                'password' => $request->input('password'),
            ]);
        }

        DB::transaction(function() use($request, $user){
            if(!$user->hasRole($request->input('role'))){
                $user->syncRoles($request->input('role'));
            }

            if($user->isDirty()){
                $user->save();
            }
        });

        return response()->noContent();
    }

    /**
     * Delete existing user record
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }
}
