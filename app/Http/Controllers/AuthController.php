<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyCodeRequest;
use App\Http\Resources\UserResource;
use App\Mail\SendMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $userRequest = $request->validated();
        $userRequest['password'] = Hash::make($userRequest['password']);

        $code = rand(100000, 999999);
        $expiredCodeAt = now()->addMinutes(10);
        $userRequest['verify_code'] = $code;
        $userRequest['expired_code_at'] = $expiredCodeAt;
        $userRequest['status'] = 0;
        $user = User::create($userRequest);

        Mail::to($user->email)->send(new SendMail($code));

        return response()->json([
            'message' => 'User created successfully',
        ], 201);
    }

    public function verifyCode(VerifyCodeRequest $request)
    {
        $userRequest = $request->validated();
        $user = User::where('email', $userRequest['email'])->first();
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        if ($user->expired_code_at < now()) {
            return response()->json([
                'message' => 'Code has expired',
            ], 400);
        }

        if ($user->verify_code !== $userRequest['code']) {
            return response()->json([
                'message' => 'Code is incorrect',
            ], 400);
        }

        $user->status = 1; // 1: verified, 0: not verified
        $user->save();

        return response()->json([
            'message' => 'Code verified successfully',
        ], 200);
    }

    public function login(LoginRequest $request)
    {
        $userRequest = $request->validated();
        $user = User::where('email', $userRequest['email'])->first();

        if (!$user || $user->status !== 1) {
            return response()->json([
                'message' => 'User not verified',
            ], 401);
        }

        $checkUser = Hash::check($userRequest['password'], $user->password);

        if (!$user || !$checkUser) {
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

    public function resendCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email not found',
            ], 404);
        }

        if ($user->status === 1) {
            return response()->json([
                'message' => 'User already verified',
            ], 400);
        }

        $verifyCode = rand(100000, 999999);

        $user->update([
            'verify_code'     => $verifyCode,
            'expired_code_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new \App\Mail\SendMail($verifyCode));

        return response()->json([
            'message' => 'Resend verify code successfully',
        ], 200);
    }
}
