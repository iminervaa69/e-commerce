<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::with([
            'store',
            'product_images' => function($query) {
                $query->orderBy('is_primary', 'desc');
            },
            'product_variants' => function($query) {
                $query->where('status', 'active')->orderBy('price', 'asc');
            },
            'product_reviews' => function($query) {
                $query->with('user')
                      ->where('status', 'approved')
                      ->latest();
            },
            'product_categories.category'
        ])
        ->where('slug', $slug)
        ->where('status', 'active')
        ->first();

        if (!$product) {
            abort(404, 'Product not found');
        }

        $breadcrumbs = $this->getBreadcrumbs($product);
        $productImages = $this->getProductImages($product);
        $productInfo = $this->getProductInfo($product);
        $productVariants = $this->getProductVariants($product);
        $sellerInfo = $this->getSellerInfo($product->store);
        $relatedProducts = $this->getRelatedProducts($product);
        $recommendedProducts = $this->getRecommendedProducts($product);
        $reviewsData = $this->getReviewsData($product);
        
        $selectedVariant = $this->getDefaultSelectedVariant($product);
        
        return view('frontend.pages.products.show', compact(
            'product',
            'breadcrumbs',
            'productImages',
            'productInfo',
            'productVariants',
            'sellerInfo',
            'relatedProducts',
            'recommendedProducts',
            'reviewsData',
            'selectedVariant' 
        ));
    }

    private function getDefaultSelectedVariant($product)
    {
        $activeVariants = $product->product_variants->where('status', 'active');
        
        if ($activeVariants->isEmpty()) {
            return null;
        }
        
        $variantWithStock = $activeVariants->where('stock', '>', 0)->first();
        $defaultVariant = $variantWithStock ?: $activeVariants->first();
        
        return [
            'id' => $defaultVariant->id,
            'name' => $defaultVariant->name,
            'sku' => $defaultVariant->sku,
            'price' => $defaultVariant->price,
            'formatted_price' => $this->formatPrice($defaultVariant->price),
            'stock' => $defaultVariant->stock,
            'attributes_display' => $this->getVariantAttributesDisplay($defaultVariant),
            'combination' => $defaultVariant->variant_combination ?? []
        ];
    }

    private function getBreadcrumbs($product)
    {
        $breadcrumbs = [
            ['label' => 'Home', 'href' => route('home')]
        ];
        
        if ($product->product_categories->isNotEmpty()) {
            $category = $product->product_categories->first()->category;
            $breadcrumbs[] = ['label' => $category->name, 'href' => '#'];
        }
        
        $breadcrumbs[] = ['label' => $product->name];
        
        return $breadcrumbs;
    }

    private function getProductImages($product)
    {
        $images = $product->product_images;
        
        if ($images->isEmpty()) {
            return [
                'main' => 'storage/photos/1/placeholder.jpg',
                'thumbnails' => [
                    ['url' => 'storage/photos/1/placeholder.jpg', 'alt' => $product->name]
                ]
            ];
        }
        
        return [
            'main' => $images->first()->image_url,
            'thumbnails' => $images->map(function ($image) use ($product) {
                return [
                    'url' => $image->image_url,
                    'alt' => $image->alt_text ?? $product->name
                ];
            })->toArray()
        ];
    }

    private function getProductInfo($product)
    {
        $activeVariants = $product->product_variants->where('status', 'active');
        
        return [
            'title' => $product->name,
            'subtitle' => $product->subtitle ?? $product->sku,
            'price' => $this->formatPrice($product->min_price),
            'rating' => round($product->average_rating, 1),
            'total_ratings' => $product->total_reviews,
            'condition' => $product->condition ?? 'Baru',
            'min_order' => $product->min_order ?? 1,
            'tags' => $product->tags->pluck('name')->toArray(),
            'description' => $product->short_description ?? $product->description,
            'stock' => $activeVariants->sum('stock'),
            'preorder_time' => $product->preorder_days ?? 0,
        ];
    }

    private function getProductVariants($product)
    {
        $activeVariants = $product->product_variants->where('status', 'active');
        
        if ($activeVariants->isEmpty()) {
            return [];
        }

        $basePrice = $activeVariants->min('price');
        
        $variantTypes = [];
        
        foreach ($activeVariants as $variant) {
            if (!$variant->variant_combination) {
                continue;
            }
            
            foreach ($variant->variant_combination as $attributeKey => $attributeValue) {
                if (!isset($variantTypes[$attributeKey])) {
                    $variantTypes[$attributeKey] = [
                        'label' => ucfirst(str_replace('_', ' ', $attributeKey)),
                        'required' => true,
                        'options' => []
                    ];
                }
                
                $optionExists = false;
                foreach ($variantTypes[$attributeKey]['options'] as &$existingOption) {
                    if ($existingOption['value'] === $attributeValue) {
                        $optionExists = true;
                        break;
                    }
                }
                
                if (!$optionExists) {
                    $option = [
                        'id' => $variant->id,
                        'value' => $attributeValue,
                        'label' => ucfirst(str_replace('_', ' ', $attributeValue)),
                        'price_diff' => $variant->price - $basePrice,
                        'stock' => $variant->stock
                    ];
                    
                    if ($attributeKey === 'color' || $attributeKey === 'warna') {
                        $option['color_code'] = $this->getColorCode($attributeValue);
                    }
                    
                    $variantTypes[$attributeKey]['options'][] = $option;
                }
            }
        }
        
        foreach ($variantTypes as &$variantType) {
            usort($variantType['options'], function($a, $b) {
                return $a['price_diff'] <=> $b['price_diff'];
            });
        }

        return $variantTypes;
    }
    
    private function getColorCode($colorName)
    {
        $colorMap = [
            // English colors
            'red' => '#EF4444',
            'blue' => '#3B82F6', 
            'green' => '#10B981',
            'yellow' => '#F59E0B',
            'purple' => '#8B5CF6',
            'pink' => '#EC4899',
            'black' => '#1F2937',
            'white' => '#F9FAFB',
            'gray' => '#6B7280',
            'grey' => '#6B7280',
            'orange' => '#F97316',
            'brown' => '#92400E',
            'navy' => '#1E3A8A',
            'lime' => '#65A30D',
            'cyan' => '#0891B2',
            'teal' => '#0D9488',
            'indigo' => '#4F46E5',
            'violet' => '#7C3AED',
            'rose' => '#F43F5E',
            'emerald' => '#059669',
            'sky' => '#0EA5E9',
            'amber' => '#D97706',
            'slate' => '#475569',
            'zinc' => '#52525B',
            'stone' => '#57534E',
            
            // Indonesian colors (common translations)
            'merah' => '#EF4444',
            'biru' => '#3B82F6',
            'hijau' => '#10B981',
            'kuning' => '#F59E0B',
            'ungu' => '#8B5CF6',
            'pink' => '#EC4899',
            'hitam' => '#1F2937',
            'putih' => '#F9FAFB',
            'abu' => '#6B7280',
            'abu-abu' => '#6B7280',
            'jingga' => '#F97316',
            'coklat' => '#92400E',
            'emas' => '#F59E0B',
            'perak' => '#9CA3AF',
        ];
        
        $lowercaseColor = strtolower(trim($colorName));
        return $colorMap[$lowercaseColor] ?? null;
    }

    private function getVariantAttributesDisplay($variant)
    {
        if (!$variant->variant_combination) {
            return null;
        }
        
        $display = [];
        foreach ($variant->variant_combination as $key => $value) {
            $display[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . ucfirst(str_replace('_', ' ', $value));
        }
        
        return implode(', ', $display);
    }

    private function getSellerInfo($store)
    {
        return [
            'name' => $store->name,
            'rating' => round($store->average_rating ?? 4.5, 1),
            'location' => $store->city ?? $store->address,
            'is_online' => $store->is_online ?? true,
            'response_time' => $store->avg_response_time ?? '5 jam pesanan diproses',
        ];
    }

    private function getRelatedProducts($product)
    {
        return Product::with([
                'store', 
                'product_images' => function($query) {
                    $query->orderBy('is_primary', 'desc');
                },
                'product_variants' => function($query) {
                    $query->where('status', 'active');
                },
                'product_reviews'
            ])
            ->where('store_id', $product->store_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->whereHas('product_variants', function($query) {
                $query->where('status', 'active');
            })
            ->limit(10)
            ->get()
            ->map(function ($relatedProduct) {
                return $this->formatProductCard($relatedProduct);
            });
    }

    private function getRecommendedProducts($product)
    {
        $categoryIds = $product->product_categories->pluck('category_id')->toArray();
        
        return Product::with([
                'store', 
                'product_images' => function($query) {
                    $query->orderBy('is_primary', 'desc');
                },
                'product_variants' => function($query) {
                    $query->where('status', 'active');
                }
            ])
            ->whereHas('product_categories', function($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            })
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->whereHas('product_variants', function($query) {
                $query->where('status', 'active');
            })
            ->limit(15)
            ->get()
            ->map(function ($recommendedProduct) {
                return $this->formatProductCard($recommendedProduct);
            });
    }

    private function getReviewsData($product)
    {
        $reviews = $product->product_reviews;
        
        $ratingsBreakdown = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingsBreakdown[$i] = $reviews->where('rating', $i)->count();
        }
        
        $satisfactionRate = $reviews->count() > 0 
            ? round(($reviews->where('rating', '>=', 4)->count() / $reviews->count()) * 100)
            : 100;
        
        return [
            'average_rating' => round($product->average_rating, 1),
            'total_reviews' => $product->total_reviews,
            'total_comments' => $reviews->whereNotNull('comment')->count(),
            'satisfaction_rate' => $satisfactionRate,
            'ratings_breakdown' => $ratingsBreakdown,
        ];
    }

    private function formatProductCard($product)
    {
        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'image' => $product->primary_image?->image_url ?? 'storage/photos/1/oracle.jpg',
            'price' => $this->formatPrice($product->min_price),
            'price_range' => $this->getPriceRange($product),
            'location' => $product->store->address ?? 'Unknown Location',
            'store_name' => $product->store->name ?? 'Unknown Store',
            'rating' => round($product->average_rating, 1),
            'total_reviews' => $product->total_reviews,
            'href' => route('product.show', $product->slug), 
            'badge' => $this->getProductBadge($product),
            'badge_type' => $this->getProductBadgeType($product)
        ];
    }

    private function formatPrice($price)
    {
        return 'Rp' . number_format($price, 0, ',', '.');
    }

    private function getPriceRange($product)
    {
        $minPrice = $product->min_price;
        $maxPrice = $product->max_price;
        
        if ($minPrice == $maxPrice) {
            return $this->formatPrice($minPrice);
        }
        
        return $this->formatPrice($minPrice) . ' - ' . $this->formatPrice($maxPrice);
    }

    private function checkIfPreorder($product)
    {
        return $product->product_variants->every(function ($variant) {
            return $variant->stock <= 0;
        });
    }

    private function getProductBadge($product)
    {
        if ($product->created_at->gt(now()->subDays(7))) {
            return 'New Arrival';
        }
        
        if ($product->product_variants->sum('stock') < 10) {
            return 'Limited Stock';
        }
        
        return null;
    }

    private function getProductBadgeType($product)
    {
        $badge = $this->getProductBadge($product);
        
        return match($badge) {
            'New Arrival' => 'success',
            'Limited Stock' => 'warning',
            default => 'primary'
        };
    }

    public function getVariant(Request $request, $productId, $variantId)
    {
        $variant = \App\Models\ProductVariant::where('product_id', $productId)
            ->where('id', $variantId)
            ->where('status', 'active')
            ->first();
        
        if (!$variant) {
            return response()->json(['error' => 'Variant not found'], 404);
        }
        
        return response()->json([
            'id' => $variant->id,
            'name' => $variant->name,
            'sku' => $variant->sku,
            'price' => $variant->price,
            'formatted_price' => $this->formatPrice($variant->price),
            'stock' => $variant->stock,
            'attributes_display' => $this->getVariantAttributesDisplay($variant),
            'combination' => $variant->variant_combination ?? []
        ]);
    }
}