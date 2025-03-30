<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Handle user login and return a token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate input credentials
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Finding user by email
        $user = User::where('email', $request->email)->first();

        // verify user and password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // generate a new token for the user
        $token = $user->createToken('API Token')->plainTextToken;

        // Devolver el token y los datos del usuario
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
