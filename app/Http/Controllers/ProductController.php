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

    public function index(Request $request)
    {
        $products = Product::orderBy('id', 'desc');

        return ProductResource::apiPaginate($products, $request);
    }

    public function update (Request $request , $id) 
    {
        $product = Product::find($id);
        if (!$product) {
            return response() -> json([
                'message' => 'Product not found'
            ],404);
        }else {
            $product->update($request->all());
            return response() -> json([
                'message' => 'Product updated successfully',
                'data' => $product
            ],201); 
        }
    }
}
