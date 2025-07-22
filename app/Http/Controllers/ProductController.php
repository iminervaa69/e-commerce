<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Store;
use App\Models\ProductVariant;
use App\Models\ProductCategory;
use Illuminate\Support\HtmlString;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('store')->paginate(10);

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
        dd($request->all());
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
        $product = Product::with([
            'product_variants',
            'categories',
            'product_images.product_variant'
        ])->find($id);

        if (!$product) {
            return redirect()->route('products.index')->with('error', 'Product not found.');
        }

        $productImages = $product->product_images->map(function ($img) {
            $url = str_replace('\\', '/', $img->image_url);

            if (preg_match('/^https?:\/\//', $url)) {
                $imageSrc = $url;
            } else {
                $imageSrc = Storage::disk('public')->url($url);
            }

            return (object)[
                'id' => $img->id,
                'product_id' => $img->product_id,
                'is_primary' => $img->is_primary_label,
                'product_variant_id' => $img->variant_name,
                'image' => new HtmlString('<img src="' . $imageSrc . '" alt="Image" class="h-12 w-auto rounded">'),
                'image_url' => $img->image_url
            ];
        });

        $stores = Store::all();

        return view('pages.products.edit', [
            'product' => $product,
            'stores' => $stores,
            'productVariants' => $product->product_variants,
            'productCategories' => $product->categories,
            'productImages' => $productImages
        ]);
    }
}
