<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @group Authentication
 */
class UserController extends Controller
{
    /**
     * get authenticated user data
     */
    public function __invoke(Request $request)
    {
        return $request->user();
    }
}
