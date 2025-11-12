<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $userRequest = $request->validated();
        $userRequest['password'] = Hash::make($userRequest['password']);
        $user = User::create($userRequest);

        return response()->json([
            'message' => 'User created successfully',
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $userRequest = $request->validated();
        $user = User::where('email', $userRequest['email'])->first();

        // Kiểm tra user tồn tại TRƯỚC khi check password
        if (!$user) {
            return response()->json([
                'message' => 'Password or email is incorrect',
            ], 401);
        }

        // Bây giờ mới check password
        $checkUser = Hash::check($userRequest['password'], $user->password);

        if (!$checkUser) {
            return response()->json([
                'message' => 'Password or email is incorrect',
            ], 401);
        }

        $accessToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'message' => 'Login successful',
            'access_token' => $accessToken,
        ], 200);
    }

    public function getProfile()
    {
        return response()->json([
            'user' => new UserResource(auth()->user()),
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }

}
