<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendCodeRequest;
use App\Http\Requests\Auth\VerifyCodeRequest;
use App\Http\Resources\UserResource;
use App\Mail\SendMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $userRequest = $request->validated();
        $userRequest['password'] = Hash::make($userRequest['password']);
        $code=rand(100000,999999);
        $expiredCodeAt=now()->addMinutes(10);
        $timeResend=now()->addMinutes(1);
        $userRequest['verify_code']=$code;
        $userRequest['expired_code_at']=$expiredCodeAt;
        $userRequest['last_resend_at']=$timeResend;
        $userRequest['status']=0;
        $user = User::create($userRequest);

        Mail::to($user->email)->send(new SendMail($code));

        return response()->json([
            'message' => 'User created successfully',
            'code' => $code,
            'expired_code_at' => $expiredCodeAt,
        ], 201);
    }

    public function verifyCode(VerifyCodeRequest $request){
        $userRequest=$request->validated();
        $user=User::where('email',$userRequest['email'])->first();

        if(!$user){
            return response()->json([
                'message' => 'User not found',
            ],404);
        }
        
        if($user->expired_code_at<now()){
            return response()->json([
                'message' => 'Code has expired',
            ],400);
        }

        if($user->verify_code !== $userRequest['code']){
            return response()->json([
                'message' => 'Code is incorrect',
            ],400);
        }

        $user->status=1;
        $user->save();

        return response()->json([
            'message' => 'Code verified successfully',
        ],200);
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
        if (!$user || $user->status !== 1) {
            return response()->json([
                'message' => 'User not verified',
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
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => "Logut successful",
        ],200);
    }
    
    public function resendCode(ResendCodeRequest $request){
        $userRequest = $request->validated();
        $user = User::where('email',$userRequest['email'])->first();
        if(!$user){
            return response()->json([
                'message'=>'User not found'
            ],404);
        }
        if($user->last_resend_at && now()->lessThan($user->last_resend_at)){
            $timeResen = $user->last_resend_at->diffInSeconds(now());
            return response()->json([
                'message' => 'Please wait '. $timeResen .' (s) to resend code',
            ], 429);
        }
        if ($user->status === 1) {
            return response()->json([
                'message' => 'User authenticated code',
            ], 400);
        }
        $code=rand(100000,999999);
        $expiredCodeAt=now()->addMinutes(10);
        $user->verify_code=$code;
        $user->expired_code_at=$expiredCodeAt;
        $user->last_resend_at=now()->addMinutes(1);
        $user->save();

        Mail::to($user->email)->send(new SendMail($code));

        return response()->json([
            'message' => 'Resent code successfully',
        ], 200);
    }
}
