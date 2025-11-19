<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class ResendCodeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|string|email|max:255'
        ];
    }
}