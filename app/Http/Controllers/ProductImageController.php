<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Store;
use App\Models\ProductVariant;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Traits\UploadTrait;

class ProductImageController extends Controller
{
    use UploadTrait;
    public function index()
    {
        return view('pages.products.images.index');
    }

    public function create($productId)
    {
        $product = Product::findOrFail($productId);
        $products = Product::all();
        $productVariants = ProductVariant::where('product_id', $productId)->get();

        return view('pages.products.images.create', compact('product', 'products', 'productVariants'));
    }

    public function getVariants($productId)
    {
        $variants = ProductVariant::where('product_id', $productId)->get();
        return response()->json($variants);
    }


    public function store(Request $request)
    {
        dd($request->all());
        $request->validate([
            'image_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'exclude_if:image_file,null|string|max:512',
            'product_id' => 'required|integer|exists:products,id',
            'product_variant_id' => 'nullable|integer|exists:product_variants,id', 
            'is_primary' => 'nullable|boolean',
        ]);

        $product = Product::findOrFail($request->product_id);

        // $slug = str_replace(' ', '_', strtolower(preg_replace('/[^A-Za-z0-9 ]/', '', $product->name)));
        // $filename = "{$product->id}_{$slug}_" . now()->format('YmdHis') . '.' . $request->file('image')->getClientOriginalExtension();
        // $path = $request->file('image')->storeAs("photos/{$product->id}/products", $filename, 'public');

        $path = $this->upload($request->file('image'), $product->id, $product->name, "photos/1/products");
        ProductImage::create([
            'image_url' => $path,
            'product_id' => $request->product_id,
            'product_variant_id' => $request->product_variant_id,
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return redirect()->route('products.edit', $request->product_id)
                         ->with('success', 'Product image created successfully.');
    }

    public function edit($id)
    {
        return view('pages.products.images.edit');
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}


//     public function store(Request $request)
//     {
//         $request->validate([
//             'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
//             'product_id' => 'required|integer|exists:products,id',
//             'product_variant_id' => 'required|integer|exists:product_variants,id',
//             'is_primary' => 'nullable|boolean',
//             'description' => 'nullable|string',
//         ]);

//         $path = $request->file('image')->store('product_images', 'public');

//         ProductImage::create([
//             'image_url' => $path,
//             'product_id' => $request->product_id,
//             'product_variant_id' => $request->product_variant_id,
//             'is_primary' => $request->boolean('is_primary'),
//             'description' => $request->description,
//         ]);

//         return redirect()->route('products.edit', $request->product_id)
//                          ->with('success', 'Product image created successfully.');
//     }

//     public function edit($id)
//     {
//         $productImage = ProductImage::findOrFail($id);

//         return view('pages.products.images.edit', [
//             'productImage' => $productImage,
//             'products' => Product::all(),
//             'variants' => ProductVariant::all(),
//             'product' => $productImage->product,
//             'variant' => $productImage->variant,
//             'defaultProductId' => $productImage->product_id,
//             'defaultVariantId' => $productImage->product_variant_id,
//         ]);
//     }

//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
//             'product_id' => 'required|integer|exists:products,id',
//             'product_variant_id' => 'required|integer|exists:product_variants,id',
//             'is_primary' => 'nullable|boolean',
//             'description' => 'nullable|string',
//         ]);

//         $productImage = ProductImage::findOrFail($id);

//         if ($request->hasFile('image')) {
//             if ($productImage->image_url && Storage::disk('public')->exists($productImage->image_url)) {
//                 Storage::disk('public')->delete($productImage->image_url);
//             }

//             $path = $request->file('image')->store('product_images', 'public');
//             $productImage->image_url = $path;
//         }

//         $productImage->product_id = $request->product_id;
//         $productImage->product_variant_id = $request->product_variant_id;
//         $productImage->is_primary = $request->boolean('is_primary');
//         $productImage->description = $request->description;
//         $productImage->save();

//         return redirect()->route('products.edit', $productImage->product_id)
//                          ->with('success', 'Product image updated successfully.');
//     }

//     public function destroy($id)
//     {
//         $productImage = ProductImage::findOrFail($id);
//         $productId = $productImage->product_id;

//         if ($productImage->image_url && Storage::disk('public')->exists($productImage->image_url)) {
//             Storage::disk('public')->delete($productImage->image_url);
//         }

//         $productImage->delete();

//         return redirect()->route('products.edit', $productId)
//                          ->with('success', 'Product image deleted successfully.');
//     }
// }