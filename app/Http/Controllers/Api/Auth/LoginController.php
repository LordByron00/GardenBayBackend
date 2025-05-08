<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle an incoming authentication request and issue a Sanctum token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            // You might add a 'device_name' field if you want to differentiate tokens per device
            // 'device_name' => 'required',
        ]);

        // Attempt to authenticate the user with provided credentials
        if (!Auth::attempt($request->only('email', 'password'))) {
            // If authentication fails, throw a validation exception
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')], // Use Laravel's built-in auth failed message
            ]);
        }

        // Get the authenticated user
        $user = Auth::user();

        // Revoke all existing tokens for the user if you want only one active token per login
        // $user->tokens()->delete();

        // Create a new Sanctum API token for the user
        // The token name is optional but can be useful for managing tokens
        $token = $user->createToken($request->device_name ?? 'api_token')->plainTextToken;

        // Return a JSON response with the user data and the plain-text token
        return response()->json([
            'user' => $user, // You can customize what user data to return
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        // Validate the incoming request data for registration
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Ensure email is unique in the users table
            'password' => 'required|string|min:8|confirmed', // 'confirmed' requires a password_confirmation field in the request
            // You might add a 'device_name' field here too
            // 'device_name' => 'required',
        ]);

        // Create the new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password before saving
        ]);

        // Optionally, log the user in immediately after registration and issue a token
        // You might choose NOT to issue a token here and require them to login separately
        $token = $user->createToken($request->device_name ?? 'api_token')->plainTextToken;

        // Return a JSON response with the new user data and the token
        return response()->json([
            'user' => $user, // You can customize what user data to return
            'token' => $token,
            'message' => 'User registered successfully',
        ], 201); // Use 201 Created status code
    }

    /**
     * Revoke the current user's API token (Logout).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Delete the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        // Return a success response
        return response()->json(['message' => 'Logged out successfully']);
    }


}
