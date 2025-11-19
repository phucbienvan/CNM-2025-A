<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendCodeRequest;
use App\Http\Requests\Auth\VerifyCodeRequest;
use App\Http\Resources\UserResource;
use App\Mail\SendCodeVerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $userInput = $request->validated();
        $userInput['password'] = Hash::make($userInput['password']);
        $userInput['code'] = Str::random(6);
        $userInput['code_expires_at'] = now()->addMinutes(10);
        $user = User::create($userInput);

        Mail::to($user->email)->send(new SendCodeVerifyEmail($user->code));

        return response()->json([
            'message' => 'User created successfully',
        ], 201);
    }

    public function verifyEmail(VerifyCodeRequest $request)
    {
        $userInput = $request->validated();
        $user = User::where('email', $userInput['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        if ($user->code !== $userInput['code']) {
            return response()->json([
                'message' => 'Invalid code',
            ], 400);
        }

        if ($user->code_expires_at < now()) {
            return response()->json([
                'message' => 'Code expired',
            ], 400);
        }

        $user->is_verified = true;
        $user->code = null;
        $user->code_expires_at = null;
        $user->save();

        return response()->json([
            'message' => 'Email verified successfully',
            'user' => new UserResource($user)
        ], 200);
    }

    public function resendCode(ResendCodeRequest $request)
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['message' => 'Email is already verified'], 400);
        }

        $user->code = Str::random(6);
        $user->code_expires_at = now()->addMinutes(10);
        $user->save(); 

        Mail::to($user->email)->send(new SendCodeVerifyEmail($user->code));

        return response()->json([
            'message' => 'Verification code has been resent to your email.',
        ], 200);
    }

    public function login(LoginRequest $request)
    {
        $userRequest = $request->validated();
        $user = User::where('email', $userRequest['email'])->first();
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
}
