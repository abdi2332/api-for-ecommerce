<?php

namespace App\Http\Controllers\API;

use App\Models\Image;
use App\Models\Product;
use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Categorie::all();
        $products = Product::with('images', 'categories')->get();

        return response()->json([
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function create()
    {
        $categories = Categorie::all();

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'images.*' => 'required',
            'price' => 'required',
            'discription' => 'required',
            'title' => 'required',
            'categorie_id' => 'required',
        ]);

        $product = Product::create($request->except('images'));
        if ($request->hasfile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                $fileName = "odaaelectronics-" . rand(100, 999) . '-' . $image->getClientOriginalName();
                $image->storeAs('public/product', $fileName);

                Image::create([
                    'product_id' => $product->id,
                    'album_id' => '1',
                    'full' => $fileName,
                ]);
            }
        }

        return response()->json($product, 201); // Return created product with 201 status code
    }

    public function show(Product $product)
    {
        $relatedProducts = Product::where('categorie_id', $product->categorie_id)->get();
        return response()->json([
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function edit($id)
    {
        $product = Product::with('images')->find($id);
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'price' => 'required',
            'discription' => 'required',
            'title' => 'required',
            'categorie_id' => 'required',
        ]);

        $product->update($validatedData);

        if ($request->hasfile('images')) {
            foreach ($product->images as $imaged) {
                Storage::delete('public/product/' . $imaged->full);
                $imaged->delete();
            }

            $images = $request->file('images');
            foreach ($images as $image) {
                $fileName = "odaaelectronics-" . rand(100, 999) . '-' . $image->getClientOriginalName();
                $image->storeAs('public/product', $fileName);

                Image::create([
                    'product_id' => $product->id,
                    'album_id' => '1',
                    'full' => $fileName,
                ]);
            }
        }

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::with('images')->find($id);

        if ($product->orders()->count() > 0) {
            return response()->json(['error' => 'Cannot delete product which has child orders'], 400);
        }

        foreach ($product->images as $imaged) {
            Storage::delete('public/product/' . $imaged->full);
            $imaged->delete();
        }
        $product->delete();

        return response()->json(['success' => 'Product deleted successfully']);
    }

    public function search(SearchRequest $request)
    {
        $products = product::where('title', 'LIKE', '%' . $request->search . '%')
            ->orWhere('discription', 'LIKE', '%' . $request->search . '%')
            ->get();
        return response()->json($products);
    }

    public function grid()
    {
        $products = Product::with('images', 'categories')->get();
        return response()->json($products);
    }
}

