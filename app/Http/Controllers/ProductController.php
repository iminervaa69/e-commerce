<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Store;
use App\Models\ProductVariant;
use App\Models\ProductCategory;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('store')->get();

        $products->each(function ($product) {
            $product->store_name = $product->store->name ?? 'N/A';
        });

        return view('pages.products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'store_id' => 'required|integer',
            'description' => 'nullable|string',
        ]);
        Product::create($request->all());
        return redirect()->route('products.index')->with('success','Product created successfully.');
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'store_id' => 'required|integer',
            'description' => 'nullable|string',
        ]);
        $product = Product::find($id);
        $product->update($request->all());
        return redirect()->route('products.edit', $product->id)->with('success', 'Product updated successfully.');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }


    public function create()
    {
        $stores = Store::all();
        return view('pages.products.create', compact('stores'));
    }
    public function edit(string $id)
    {
        $product = Product::with(['product_variants', 'categories'])->find($id);

        if (!$product) {
            return redirect()->route('products.index')->with('error', 'Product not found.');
        }

        $highestProductPrice = $product->product_variants->max('price');
        $lowestProductPrice = $product->product_variants->min('price');
        $stores = Store::all();

        return view('pages.products.edit', [
            'product' => $product,
            'stores' => $stores,
            'productVariants' => $product->product_variants,
            'productCategories' => $product->categories, // cleaner!
            'highestProductPrice' => $highestProductPrice,
            'lowestProductPrice' => $lowestProductPrice,
        ]);
    }
}
