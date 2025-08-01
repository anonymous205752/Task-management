<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // Remove bcrypt if you're using 'password' => 'hashed' in model casts
            'password' => $request->password,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully',
            ]);
        }

        return response()->json([
            'message' => 'No active token found.',
        ], 400);
    }
    public function updateProfile(Request $request)
{
    $user = $request->user();

    $request->validate([
        'name' => 'string|max:255',
        'email' => 'email|unique:users,email,' . $user->id,
    ]);

    $user->update($request->only(['name', 'email']));

    return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
}
public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:6|confirmed',
    ]);

    $user = $request->user();

    if (! Hash::check($request->current_password, $user->password)) {
        return response()->json(['message' => 'Current password is incorrect'], 403);
    }

    $user->update([
        'password' => $request->new_password, // auto-hashed via casts
    ]);

    return response()->json(['message' => 'Password changed successfully']);
}


}
