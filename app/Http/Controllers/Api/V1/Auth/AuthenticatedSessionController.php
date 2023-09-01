<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @group Authentication
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Request auth token
     * @unauthenticated
     *
     * @bodyParam email string required valid email. Example: test@example.com
     * @bodyParam password string required secured password. Example: password
     * @return JsonResponse
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $device    = substr($request->userAgent() ?? '', 0, 255);
        $expiresAt = $request->remember ? null : now()->addMinutes(config('session.lifetime'));

        return response()->json([
            'auth_token' => $request->user()->createToken($device, expiresAt: $expiresAt)->plainTextToken
        ]);
    }

    /**
     * Destroy auth token.
     */
    public function destroy(Request $request): Response
    {
        $request->user()->tokens()->delete();
        return response()->noContent();
    }
}
