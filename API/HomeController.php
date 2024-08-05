<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\categorie;
use App\Models\product;

class HomeController extends Controller
{
    public function index()
    {
        $categorie = categorie::all();
        $product = product::with('categories', 'images', 'popular', 'newadded')->get();

        return response()->json([
            'categorie' => $categorie,
            'product' => $product,
        ]);
    }

    public function allproduct()
    {
        $product = product::with('images', 'categories')->get();
        return response()->json([
            'product' => $product
        ]);
    }

    public function category(CategoryRequest $request, $id)
    {
        $product = product::where('categorie_id', $id)->with('images', 'categories')->get();

        return response()->json(['product' => $product]);
    }
}
