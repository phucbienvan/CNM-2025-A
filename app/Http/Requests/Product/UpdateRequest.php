<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
        ];
    }

    public function messages()
    {
        return [
            'numeric' => ':attribute: must be number',
            'string' => ':attribute: must be string',
            'max' => ':attribute: must be less than 255 characters',
        ];
    }
}
