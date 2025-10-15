<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\CreateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(CreateRequest $request)
    {
        $userRequest = $request->validated();
        $product = Product::create($userRequest);

        return new ProductResource($product);
    }
}
