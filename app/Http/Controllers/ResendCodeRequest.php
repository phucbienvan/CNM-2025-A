<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class ResendCodeRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'email' => 'required|string|email|exists:users,email',
        ];
    }
}