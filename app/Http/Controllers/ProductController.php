<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\CreateRequest;
use App\Http\Requests\Product\UpdateRequest;
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

    public function update(UpdateRequest $request, $id)
    {
        if(!Product::where('id', $id)->exists())
        {
            abort(404, 'Product not found');
        }
        $userRequest = $request->validated();
        $product = Product::findOrFail($id);
        $product->update($userRequest);
        return new ProductResource($product);
    }

    public function destroy($id)
    {
        if(!Product::where('id', $id)->exists())
        {
            abort(404, 'Product not found');
        }
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
