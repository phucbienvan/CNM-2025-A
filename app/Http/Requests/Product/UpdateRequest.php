<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

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
            'price' => 'sometimes|numeric'
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'Name must be string',
            'name.max' => 'Name must be less than 255 characters',
            'description.string' => 'Description must be string',
            'price.numeric' => 'Price must be number'
        ];
    }
}
