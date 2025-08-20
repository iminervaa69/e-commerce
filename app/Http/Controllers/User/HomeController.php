<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Carousel;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the homepage
     */
    public function index()
    {
        // Get carousel images for homepage banner
        $carouselImages = $this->getCarouselImages();
        
        // Get featured products for the grid
        $products = $this->getFeaturedProducts();
        
        // Get popular categories (optional)
        $popularCategories = $this->getPopularCategories();
        
        return view('frontend.pages.home', compact('carouselImages', 'products', 'popularCategories'));
    }

    /**
     * Get carousel images for homepage
     */
    private function getCarouselImages()
    {
        $carousels = Carousel::currentlyActive()
                          ->ordered()
                          ->get();

        // If no carousel data exists, return default/fallback images
        if ($carousels->isEmpty()) {
            return collect([
                [
                    'src' => 'storage/photos/1/exusiai-1.png',
                    'alt' => 'Featured Product 1'
                ],
                [
                    'src' => 'storage/photos/1/exusiai-2.jpg',
                    'alt' => 'Featured Product 2'
                ],
                [
                    'src' => 'storage/photos/1/exusiai-3.jpg',
                    'alt' => 'Featured Product 3'
                ]
            ]);
        }
        
        // Transform to match your current view format
        return $carousels->map(function ($carousel) {
            return [
                'src' => $carousel->image_url,
                'alt' => $carousel->title ?? 'Banner Image',
                'title' => $carousel->title,
                'subtitle' => $carousel->subtitle,
                'link_url' => $carousel->link_url,
                'link_text' => $carousel->link_text ?? 'Learn More'
            ];
        });
    }

    /**
     * Get featured products for homepage grid
     */
    private function getFeaturedProducts()
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
            ->whereHas('store', function($query) {
                $query->where('status', 'active');
            })
            ->whereHas('product_variants', function($query) {
                $query->where('status', 'active')
                      ->where('stock', '>', 0);
            })
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($product) {
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
                    'is_preorder' => $this->checkIfPreorder($product),
                    'badge' => $this->getProductBadge($product),
                    'badge_type' => $this->getProductBadgeType($product)
                ];
            });
    }

    /**
     * Get popular categories (optional)
     */
    private function getPopularCategories()
    {
        return Category::whereHas('product_categories.product.product_variants', function($query) {
                $query->where('status', 'active');
            })
            ->withCount(['product_categories as products_count'])
            ->orderBy('products_count', 'desc')
            ->limit(8)
            ->get();
    }

    /**
     * Helper methods for product formatting
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
        // Check if all variants are out of stock or have future availability
        return $product->product_variants->every(function ($variant) {
            return $variant->stock <= 0;
        });
    }

    private function getProductBadge($product)
    {
        // Add your badge logic here
        // Examples: "New", "Sale", "Limited Stock", etc.
        
        if ($product->created_at->gt(now()->subDays(7))) {
            return 'New Arrival';
        }
        
        if ($product->product_variants->sum('stock') < 10) {
            return 'Limited Stock';
        }
        
        // You can add discount/promo logic here
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