<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Class Tag
 * 
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $color
 * @property bool|null $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Product[] $products
 *
 * @package App\Models
 */
class Tag extends Model
{
    use SoftDeletes;
    
    protected $table = 'tags';

    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected $fillable = [
        'name',
        'slug',
        'color',
        'is_active'
    ];

    protected $attributes = [
        'color' => '#3B82F6',
        'is_active' => true
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = static::generateUniqueSlug($tag->name);
            }
        });
        
        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                $tag->slug = static::generateUniqueSlug($tag->name);
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

    // Relationships
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tags', 'tag_id', 'product_id')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    // Route key binding for slug URLs
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Helper methods
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    public function getColorStyleAttribute()
    {
        return "background-color: {$this->color}; color: " . $this->getContrastColor();
    }

    private function getContrastColor()
    {
        $hex = str_replace('#', '', $this->color);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }
}