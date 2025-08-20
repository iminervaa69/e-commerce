<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductReview;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Display product detail page
     */
    public function show($slug)
    {
        // Get product with all necessary relationships
        $product = Product::with([
            'store',
            'product_images' => function($query) {
                $query->orderBy('is_primary', 'desc');
            },
            'product_variants' => function($query) {
                $query->where('status', 'active');
            },
            'product_reviews' => function($query) {
                $query->with('user')
                      ->where('status', 'approved')
                      ->latest();
            },
            'categories',
            'product_categories.category'
        ])
        ->where('slug', $slug)
        ->where('status', 'active')
        ->first();

        if (!$product) {
            abort(404, 'Product not found');
        }

        // Get breadcrumb data
        $breadcrumbs = $this->getBreadcrumbs($product);
        
        // Get product images for gallery
        $productImages = $this->getProductImages($product);
        
        // Get product info
        $productInfo = $this->getProductInfo($product);

        // $productVariants = $this->getProductVariants($product);
        
        // Get seller info
        $sellerInfo = $this->getSellerInfo($product->store);
        
        // Get related products from same store
        $relatedProducts = $this->getRelatedProducts($product);
        
        // Get other recommended products
        $recommendedProducts = $this->getRecommendedProducts($product);
        
        // Get reviews data
        $reviewsData = $this->getReviewsData($product);
        
        // Get tab content data
        $tabsData = $this->getTabsData($product);
        
        return view('frontend.pages.detail', compact(
            'product',
            'breadcrumbs',
            'productImages',
            'productInfo',
            'sellerInfo',
            'relatedProducts',
            'recommendedProducts',
            'reviewsData',
            'tabsData'
            // 'productVariants'
        ));
    }

    /**
     * Get tabs content data
     */
    private function getTabsData($product)
    {
        $specifications = $product->specifications;
        if (!empty($specifications)) {
            $decodedSpecs = json_decode($specifications, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedSpecs)) {
                $specifications = $decodedSpecs;
            }
        } else {
            $specifications = [];
        }

        return [
            'details' => $product->description ?? 'No detailed description available.',
            'specifications' => $specifications,
            'important_info' => $product->important_info ?? 'No important information available.'
        ];
    }

    // private function getProductVariants($product)
    // {
    //     return $product->product_variants->map(function ($variant) {
    //         return [
    //             'id' => $variant->id,
    //             'label' => $variant->name,
    //             'sku' => $variant->sku,
    //             'price' => $this->formatPrice($variant->price),
    //             'stock' => $variant->stock,
    //         ];
    //     })->toArray();
    // }

    /**
     * Generate breadcrumbs for product
     */
    private function getBreadcrumbs($product)
    {
        $breadcrumbs = [
            ['label' => 'Home', 'href' => route('home')]
        ];
        
        // Add category breadcrumbs
        if ($product->product_categories->isNotEmpty()) {
            $category = $product->product_categories->first()->category;
            
            // You can build a more complex category hierarchy here
            $breadcrumbs[] = ['label' => $category->name, 'href' => '#'];
        }
        
        // Add current product (no href for current page)
        $breadcrumbs[] = ['label' => $product->name];
        
        return $breadcrumbs;
    }

    /**
     * Get product images for gallery
     */
    private function getProductImages($product)
    {
        $images = $product->product_images;
        
        if ($images->isEmpty()) {
            // Return default placeholder
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

    /**
     * Get formatted product information
     */
    private function getProductInfo($product)
    {
        $activeVariants = $product->product_variants->where('status', 'active');
        
        return [
            'title' => $product->name,
            'subtitle' => $product->subtitle ?? $product->sku,
            'price' => $this->formatPrice($product->min_price),
            'price_range' => $this->getPriceRange($product),
            'rating' => round($product->average_rating, 1),
            'total_ratings' => $product->total_reviews,
            'condition' => $product->condition ?? 'Baru',
            'min_order' => $product->min_order ?? 1,
            'max_order' => $product->max_order ?? 99,
            'tags' => $product->tags->pluck('name')->toArray(),
            'description' => $product->short_description ?? $product->description,
            'stock' => $activeVariants->sum('stock'),
            'preorder_time' => $product->preorder_days ?? 0,
            'weight' => $product->weight ?? 0,
            'dimensions' => [
                'length' => $product->length ?? 0,
                'width' => $product->width ?? 0,
                'height' => $product->height ?? 0
            ]
        ];
    }

    /**
     * Get seller information
     */
    private function getSellerInfo($store)
    {
        return [
            'name' => $store->name,
            'rating' => round($store->average_rating ?? 4.5, 1),
            'location' => $store->city ?? $store->address,
            'is_online' => $store->is_online ?? true,
            'response_time' => $store->avg_response_time ?? '5 jam pesanan diproses',
            'join_date' => $store->created_at,
            'total_products' => $store->products()->where('status', 'active')->count()
        ];
    }

    /**
     * Get related products from same store
     */
    private function getRelatedProducts($product)
    {
        return Product::with([
                'store', 
                'product_images' => function($query) {
                    $query->orderBy('is_primary', 'desc');
                },
                'product_variants' => function($query) {
                    $query->where('status', 'active');
                }
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

    /**
     * Get recommended products
     */
    private function getRecommendedProducts($product)
    {
        // Get products from same categories
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

    /**
     * Get reviews data
     */
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
            'recent_reviews' => $reviews->take(5)->map(function ($review) {
                return [
                    'user_name' => $review->user->name ?? 'Anonymous',
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at,
                    'helpful_count' => $review->helpful_count ?? 0
                ];
            })
        ];
    }

    /**
     * Format product card data
     */
    private function formatProductCard($product)
    {
        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'image' => $product->primary_image?->image_url ?? 'storage/photos/1/placeholder.jpg',
            'price' => $this->formatPrice($product->min_price),
            'price_range' => $this->getPriceRange($product),
            'location' => $product->store->city ?? 'Unknown Location',
            'store_name' => $product->store->name ?? 'Unknown Store',
            'rating' => round($product->average_rating, 1),
            'total_reviews' => $product->total_reviews,
            'href' => route('product.show', $product->slug),
            'is_preorder' => $this->checkIfPreorder($product),
            'badge' => $this->getProductBadge($product),
            'badge_type' => $this->getProductBadgeType($product)
        ];
    }

    /**
     * Helper methods (same as in HomeController)
     */
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
        
        if ($badge === 'New Arrival') {
            return 'success';
        }
        
        if ($badge === 'Limited Stock') {
            return 'warning';
        }
        
        return 'primary';
    }
}