<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\CreateRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function update(UpdateRequest $request,$id){
        $product=Product::findOrFail($id);
        $data=$request->validated();
        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => new ProductResource($product)
        ]);
    }
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
}
