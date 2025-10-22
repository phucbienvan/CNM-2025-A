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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ];
    }
    public function messages()
    {
        return [
            'price.numeric' => 'Price must be number',
            'name.required' => 'Name is required',
            'description.required' => 'Description is required',
            'price.required' => 'Price is required',
            'name.string' => 'Name must be string',
            'description.string' => 'Description must be string',
            'name.max' => 'Name must be less than 255 characters',
        ];
    }
}
