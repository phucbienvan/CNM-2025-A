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

    public function update(UpdateRequest $request, Product $product)
    {
        // dd('abc');
        $data = $request->validated();
        $changes = [];

        foreach (['name', 'description', 'price'] as $field) {
            if (array_key_exists($field, $data) && $product->{$field} != $data[$field]) {
                $changes[$field] = $data[$field];
            }
        }

        if (!empty($changes)) {
            $product->update($changes);
        }

        return new ProductResource($product);
    }
}
