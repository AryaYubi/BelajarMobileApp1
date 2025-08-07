<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request)
    {

        try {
             $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                // 'phone' => ['nullable', 'string', 'max:12'],
                'password' => ['required', 'string', Password::min(8)],
                 ]);

            $user = \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                // 'phone' => $request->phone,
                'password' => Hash::make($request->password),
                ]);

            $user = \App\Models\User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('auth_token')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'User registered successfully');

        }
        catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'Something went wrong',
                    'error' => $error->getMessage(),
                ],
                'User registration failed',500
        );

        }
    }
}
