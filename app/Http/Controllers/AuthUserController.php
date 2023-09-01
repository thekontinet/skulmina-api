<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

/**
 * @group Authentication
 */
class AuthUserController extends Controller
{
    /**
     * Get authenticated user.
     */
    public function __invoke(Request $request)
    {
        return new UserResource($request->user());
    }
}
