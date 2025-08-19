<?php

/**
 * Updated Product Model
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Class Product
 * 
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int|null $store_id
 * @property string|null $description
 * @property string|null $specifications
 * @property string|null $important_info
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Store|null $store
 * @property Collection|ProductCategory[] $product_categories
 * @property Collection|ProductImage[] $product_images
 * @property Collection|ProductReview[] $product_reviews
 * @property Collection|ProductVariant[] $product_variants
 * @property Collection|Category[] $categories
 *
 * @package App\Models
 */
class Product extends Model
{
    use SoftDeletes;
    
    protected $table = 'products';

    protected $casts = [
        'store_id' => 'int'
    ];

    protected $fillable = [
        'name',
        'slug',
        'store_id',
        'description',
        'specifications',    // New field
        'important_info'     // New field
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });
        
        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });
    }

    public static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    // Original relationships (keeping your existing structure)
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function product_categories(): HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function product_images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function product_reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function product_variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Enhanced relationships for easier access
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('is_primary', 'desc');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('status', 'active');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    // Route key binding for slug URLs
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Helper methods
    public function getMainCategoryAttribute()
    {
        return $this->categories()->whereNull('parent_id')->first();
    }

    public function getCategoryPathAttribute()
    {
        $mainCategory = $this->main_category;
        if (!$mainCategory) return collect();

        $path = collect();
        $current = $mainCategory;
        
        while ($current) {
            $path->prepend($current);
            $current = $current->parent;
        }
        
        return $path;
    }

    public function getPrimaryImageAttribute()
    {
        return $this->product_images()->where('is_primary', true)->first() 
            ?: $this->product_images()->first();
    }

    public function getMinPriceAttribute()
    {
        return $this->product_variants()->min('price') ?: 0;
    }

    public function getMaxPriceAttribute()
    {
        return $this->product_variants()->max('price') ?: 0;
    }

    public function getAverageRatingAttribute()
    {
        return $this->product_reviews()->avg('rating') ?: 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->product_reviews()->count();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id')
                    ->withTimestamps();
    }

    // You can also add this helper method to get active tags only
    public function activeTags(): BelongsToMany
    {
        return $this->tags()->where('is_active', true);
    }

    /**
     * Get formatted specifications for display
     */
    public function getFormattedSpecificationsAttribute()
    {
        if (empty($this->specifications)) {
            return 'No specifications available.';
        }
        
        // If specifications is stored as JSON, decode it
        if ($this->isJson($this->specifications)) {
            return json_decode($this->specifications, true);
        }
        
        // If it's HTML or plain text, return as is
        return $this->specifications;
    }

    /**
     * Get formatted important info for display
     */
    public function getFormattedImportantInfoAttribute()
    {
        if (empty($this->important_info)) {
            return 'No important information available.';
        }
        
        return $this->important_info;
    }

    /**
     * Check if string is valid JSON
     */
    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}