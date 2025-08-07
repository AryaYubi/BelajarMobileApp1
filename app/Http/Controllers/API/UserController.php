<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
                'phone' => ['nullable', 'string', 'max:12'],
                'password' => ['required', 'string', Password::min(8)],
                 ]);

            $user = \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'phone' => $request->phone,
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
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]);
            $credentials = $request->only('email', 'password');

                if (!Auth::attempt($credentials)) {
                    return ResponseFormatter::error(
                        [
                            'message' => 'Unauthorized',
                        ],
                        'Auth Failed', 500
                    );

                } else {
                    $user = User::where('email', $request->email)->first();
                    if(!Hash::check($request->password, $user->password,[])){
                        throw new \Exception('Invalid credentials');
                    }
                    $tokenResult = $user->createToken('auth_token')->plainTextToken;

                    return ResponseFormatter::success([
                        'access_token' => $tokenResult,
                        'token_type' => 'Bearer',
                        'user' => $user,
                    ], 'Auth Success');
                }

        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'Something went wrong',
                    'error' => $error->getMessage(),
                ],
                'Authentication failed', 500
            );
        }
    }
    public function fetch(Request $request)
    {
        return ResponseFormatter::success(
            $request->user(),
            'User data fetched successfully'
        );
    }

    public function update(Request $request)
    {
        $data = $request->all();

        $user = Auth::user();
        $user->update($data);

        return ResponseFormatter::success(
            $user,
            'User profile updated successfully'
        );
    }
}
