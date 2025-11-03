<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\CreateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\CssSelector\Node\FunctionNode;

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

    public function update (Request $request , Product $product) 
    {
        $userRequest = $request->validated();
        $product->update($userRequest);

        return new ProductResource($product);
    }

    public function show(Request $request, Product $product)
    {
        return new ProductResource($product);
    }

    public function destroy(Request $request, Product $product)
    {
        $product->delete();
        
        return response()->json([
            'message'=>'Product deleted successfully!'
        ],200);
    }
}
