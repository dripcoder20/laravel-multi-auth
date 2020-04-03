<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;

class AuthController extends Controller
{

    public function store()
    {
        if ($token = auth()->attempt(request()->only('email', 'password'))) {
            return response()->json([
                'token' => $token,
                'message' => "Login successful"
            ]);
        }

        throw new AuthorizationException("Invalid Credentials");
    }

    public function show()
    {
        return response()->json(['data' => ['user' => auth()->user()]]);
    }
}
