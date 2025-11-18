<?php

namespace App\Http\Controllers;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyCodeRequest;
use App\Http\Resources\UserResource;
use App\Mail\SendMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $userRequest = $request->validated();
        $userRequest['password'] = Hash::make($userRequest['password']);
        $verifyCode = random_int(100000, 999999);
        $userRequest['verify_code'] = $verifyCode;
        $userRequest['expired_code_at'] = Carbon::now()->addMinutes(5);
        $userRequest['status'] = 0;
        $user = User::create($userRequest);

        Mail::to($user->email)->send(new SendMail($verifyCode));

        return response()->json([
            'message' => 'User created successfully',
        ], 201);
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
    public function logout(Request $request)
    {

    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logout successful',
    ], 200);
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

        if ($user->verify_code != $userRequest['verify_code']) {
            return response()->json([
                'message' => 'Invalid verify code',
            ], 400);
        }

        if (Carbon::now()->greaterThan($user->expired_code_at)) {
            return response()->json([
                'message' => 'Verify code has expired',
            ], 400);
        }

        $user->update([
            'status' => 1,
            'verify_code' => null,
            'expired_code_at' => null,
        ]);

        return response()->json([
            'message' => 'Verify code successfully',
        ], 200);
    }

    public function resendCode(Request $request)
    {
        $userRequest = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $userRequest['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        if ($user->status == 1) {
            return response()->json([
                'message' => 'User is already verified',
            ], 400);
        }

        $verifyCode = random_int(100000, 999999);
        $user->update([
            'verify_code' => $verifyCode,
            'expired_code_at' => Carbon::now()->addMinutes(5),
        ]);

        Mail::to($user->email)->send(new SendMail($verifyCode));

        return response()->json([
            'message' => 'Resend code successfully',
        ], 200);
    }

}
