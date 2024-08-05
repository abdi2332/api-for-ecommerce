<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\cart;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = \Cart::getContent();
        return response()->json($cartItems);
    }

    public function store(StoreCartRequest $request)
    {
        \Cart::add([
            'id' => $request->id,
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'attributes' => [
                'discription' => $request->discription,
                'image' => $request->image,
            ],
        ]);

        return response()->json(['success' => 'Product is Added to Cart Successfully!']);
    }

    public function updateCart(UpdateCartRequest $request, $id)
    {
        \Cart::update(
            $id,
            [
                'quantity' => [
                    'relative' => false,
                    'value' => $request->quantity,
                ],
            ]
        );

        return response()->json(['success' => 'Item Cart is Updated Successfully!']);
    }

    public function destroy($id)
    {
        \Cart::remove($id);
        return response()->json(['success' => 'Item Cart Remove Successfully!']);
    }

    public function clearAllCart()
    {
        \Cart::clear();
        return response()->json(['success' => 'All Item Cart Clear Successfully!']);
    }
}

