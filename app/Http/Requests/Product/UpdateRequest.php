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
<<<<<<< HEAD
<<<<<<< HEAD
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
        ];
    }

=======
=======
>>>>>>> 2e6c9f7fa3bf9b4cfebecf2dd0a1b19589c9a011
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
        ];
    }   
    
<<<<<<< HEAD
>>>>>>> 2e6c9f7 (add function update product)
=======
>>>>>>> 2e6c9f7fa3bf9b4cfebecf2dd0a1b19589c9a011
    public function messages()
    {
        return [
            'price.numeric' => 'Price must be number',
<<<<<<< HEAD
<<<<<<< HEAD
=======
            'name.required' => 'Name is required',
            'description.required' => 'Description is required',
            'price.required' => 'Price is required',
>>>>>>> 2e6c9f7 (add function update product)
=======
            'name.required' => 'Name is required',
            'description.required' => 'Description is required',
            'price.required' => 'Price is required',
>>>>>>> 2e6c9f7fa3bf9b4cfebecf2dd0a1b19589c9a011
            'name.string' => 'Name must be string',
            'description.string' => 'Description must be string',
            'name.max' => 'Name must be less than 255 characters',
        ];
    }
}
