<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantController extends Controller
{
    public function index()
    {
        $productVariants = ProductVariant::get();
        return view('pages.products.edit', compact('productVariants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'required|integer',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|string|in:active,inactive'
        ]);
        ProductVariant::create($request->all());
        return redirect()->route('products.edit')->with('success', 'Product variant created successfully.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'required|integer',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|string|in:active,inactive'
        ]);
        $productVariant = ProductVariant::find($id);
        $productVariant->update($request->all());
        return redirect()->route('products.edit')->with('success', 'Product variant updated successfully.');
    }

    public function destroy(string $id)
    {
        $productVariant = ProductVariant::find($id);
        if ($productVariant) {
            $productVariant->delete();
            return redirect()->route('products.edit')->with('success', 'Product variant deleted successfully.');
        }
        return redirect()->route('products.edit')->with('error', 'Product variant not found.');
    }

    public function create()
    {
        return view('pages.products.variants.create');
    }
    public function edit(string $id)
    {
        $variant = ProductVariant::find($id);
        $products = Product::with('store')->get();
        return view('pages.products.variants.edit', compact('variant', 'products'));
    }
    
}
