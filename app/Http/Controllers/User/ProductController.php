<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

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
            'product_categories.category'
        ])
        ->where('slug', $slug)
        ->where('status', 'active')
        ->first();

        if (!$product) {
            abort(404, 'Product not found');
        }

        // Get all required data
        $breadcrumbs = $this->getBreadcrumbs($product);
        $productImages = $this->getProductImages($product);
        $productInfo = $this->getProductInfo($product);
        $sellerInfo = $this->getSellerInfo($product->store);
        $relatedProducts = $this->getRelatedProducts($product);
        $recommendedProducts = $this->getRecommendedProducts($product);
        $reviewsData = $this->getReviewsData($product);
        
        return view('frontend.pages.detail', compact(
            'product',
            'breadcrumbs',
            'productImages',
            'productInfo',
            'sellerInfo',
            'relatedProducts',
            'recommendedProducts',
            'reviewsData'
        ));
    }

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
            $breadcrumbs[] = ['label' => $category->name, 'href' => '#'];
        }
        
        // Add current product
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
            'rating' => round($product->average_rating, 1),
            'href' => route('product.show', $product->slug),
            'is_preorder' => $this->checkIfPreorder($product),
            'badge' => $this->getProductBadge($product),
            'badge_type' => $this->getProductBadgeType($product)
        ];
    }

    /**
     * Helper methods
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
        
        return match($badge) {
            'New Arrival' => 'success',
            'Limited Stock' => 'warning',
            default => 'primary'
        };
    }
}