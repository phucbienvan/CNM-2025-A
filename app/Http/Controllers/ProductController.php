<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\CreateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\Product\UpdateRequest;

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
        //dd('123');
        $userRequest = $request->validated();
        $product = Product::findOrFail($id);
       $dataToUpdate = [];
        if(isset($userRequest['name'])) {
            $dataToUpdate['name'] = $userRequest['name'];
        }
        if(isset($userRequest['description'])) {
            $dataToUpdate['description'] = $userRequest['description'];
        }
        if(isset($userRequest['price'])) {
            $dataToUpdate['price'] = $userRequest['price'];
        }
        if(!empty($dataToUpdate)) {
            $product->update($dataToUpdate); //nếu có dữ liệu để cập nhật thì mới gọi hàm update
        } 
        return new ProductResource($product);
    }
}
