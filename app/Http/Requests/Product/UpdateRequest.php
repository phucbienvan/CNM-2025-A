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
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
        ];
    }
    public function messages()
    {
        return [
            'required'=>':attribute không được để trống',
            'numeric' => ':attribute must be number',
            'string' => ':attribute must be string',
            'max' => ':attribute must be less than 255 characters',
        ];
    }
    public function attributes()
    {
        return [
            'name'=> 'Name',
            'description'=> 'Description',
            'price'=> 'Price',
        ];
    }
}
