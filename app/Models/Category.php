<?php

/**
 * Updated Category Model
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Class Category
 * 
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int|null $parent_id
 * @property int $sort_order
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Category|null $parent
 * @property Collection|Category[] $children
 * @property Collection|ProductCategory[] $product_categories
 * @property Collection|Product[] $products
 *
 * @package App\Models
 */
class Category extends Model
{
    use SoftDeletes;
    
    protected $table = 'categories';

    protected $casts = [
        'parent_id' => 'int',
        'sort_order' => 'int',
        'is_active' => 'boolean'
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'sort_order',
        'is_active'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name);
            }
        });
        
        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name);
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
    public function product_categories(): HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }

    // Hierarchical relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function allChildren(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->orderBy('sort_order');
    }

    // Enhanced relationships
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_categories', 'category_id', 'product_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrderedBySort($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Route key binding for slug URLs
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Helper methods
    public function getAncestors()
    {
        $ancestors = collect();
        $current = $this->parent;
        
        while ($current) {
            $ancestors->prepend($current);
            $current = $current->parent;
        }
        
        return $ancestors;
    }

    public function getDescendants()
    {
        $descendants = collect();
        
        foreach ($this->allChildren as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getDescendants());
        }
        
        return $descendants;
    }

    public function getBreadcrumb()
    {
        $breadcrumb = $this->getAncestors();
        $breadcrumb->push($this);
        
        return $breadcrumb;
    }

    public function getFullPath()
    {
        return $this->getBreadcrumb()->pluck('slug')->implode('/');
    }

    public function hasChildren()
    {
        return $this->children()->exists();
    }

    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    public function getLevel()
    {
        return $this->getAncestors()->count();
    }

    public function getTotalProductsAttribute()
    {
        // Count products in this category and all subcategories
        $categoryIds = collect([$this->id])
            ->merge($this->getDescendants()->pluck('id'));
            
        return Product::whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        })->count();
    }

    // Get category tree structure (useful for navigation menus)
    public static function getTree($parentId = null)
    {
        return static::where('parent_id', $parentId)
            ->active()
            ->orderedBySort()
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'full_path' => $category->getFullPath(),
                    'children' => static::getTree($category->id)
                ];
            });
    }
}