<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Class ProductVariant
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $product_id
 * @property string|null $sku
 * @property string|null $description
 * @property array|null $variant_combination
 * @property float $price
 * @property int $stock
 * @property string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 *
 * @property Product $product
 * @property Collection|Attachment[] $attachments
 * @property Collection|CartItem[] $cart_items
 * @property Collection|ItemShipmentStatus[] $item_shipment_statuses
 * @property Collection|PaidItemBystore[] $paid_item_bystores
 * @property Collection|ProductImage[] $product_images
 * @property Collection|ProductReview[] $product_reviews
 *
 * @package App\Models
 */
class ProductVariant extends Model
{
    use SoftDeletes;
    protected $table = 'product_variants';

    protected $casts = [
        'product_id' => 'int',
        'price' => 'float',
        'stock' => 'int',
        'reserved_stock' => 'int',  // ADD THIS
        'sold_count' => 'int',      // ADD THIS
        'variant_combination' => 'array',
    ];

    protected $fillable = [
        'name',
        'slug',
        'product_id',
        'sku',
        'description',
        'variant_combination',
        'price',
        'stock',
        'reserved_stock',  // ADD THIS
        'sold_count',      // ADD THIS
        'status'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variant) {
            if (empty($variant->slug)) {
                $variant->slug = $variant->generateSlug($variant->name);
            }

            // Auto-generate SKU if not provided
            if (empty($variant->sku)) {
                $variant->sku = $variant->generateSku();
            }
        });

        static::updating(function ($variant) {
            if ($variant->isDirty('name') && empty($variant->slug)) {
                $variant->slug = $variant->generateSlug($variant->name);
            }
        });
    }

    /**
     * Generate slug for the product variant
     * Note: We're not enforcing uniqueness here due to existing data duplicates
     */
    private function generateSlug($name)
    {
        return Str::slug($name);
    }

    /**
     * Generate unique SKU for the product variant
     */
    private function generateSku()
    {
        $product = $this->product ?? Product::find($this->product_id);
        $baseSlug = $product ? Str::slug($product->name) : 'PROD';

        // If variant_combination exists, create SKU from attributes
        if ($this->variant_combination) {
            $attributeParts = [];
            foreach ($this->variant_combination as $key => $value) {
                $attributeParts[] = strtoupper(substr($value, 0, 3));
            }
            $suffix = implode('-', $attributeParts);
        } else {
            // Fallback: use variant name or random string
            $suffix = $this->name ? Str::slug($this->name) : Str::random(6);
        }

        $baseSku = strtoupper($baseSlug . '-' . $suffix);

        // Ensure uniqueness
        $sku = $baseSku;
        $counter = 1;
        while (self::where('sku', $sku)->where('id', '!=', $this->id ?? 0)->exists()) {
            $sku = $baseSku . '-' . $counter;
            $counter++;
        }

        return $sku;
    }

    /**
     * Get human-readable attribute display
     *
     * @return string
     */
    public function getAttributesDisplayAttribute()
    {
        if (!$this->variant_combination) {
            return null;
        }

        $display = [];
        foreach ($this->variant_combination as $key => $value) {
            // Try to get display value from product's variant_attributes
            $productAttributes = $this->product->variant_attributes ?? [];
            $displayValue = $this->getDisplayValue($key, $value, $productAttributes);

            $display[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $displayValue;
        }

        return implode(', ', $display);
    }

    /**
     * Get display value for an attribute
     */
    private function getDisplayValue($attributeKey, $value, $productAttributes)
    {
        if (isset($productAttributes[$attributeKey]['options'])) {
            foreach ($productAttributes[$attributeKey]['options'] as $option) {
                if ($option['value'] === $value) {
                    return $option['display'] ?? ucfirst($value);
                }
            }
        }

        return ucfirst(str_replace('_', ' ', $value));
    }

    /**
     * Check if variant has specific attribute value
     *
     * @param string $attribute
     * @param string $value
     * @return bool
     */
    public function hasVariantAttribute($attribute, $value)
    {
        return isset($this->variant_combination[$attribute]) &&
               $this->variant_combination[$attribute] === $value;
    }

    /**
     * Get variant attribute value
     *
     * @param string $attribute
     * @return mixed|null
     */
    public function getVariantAttribute($attribute)
    {
        return $this->variant_combination[$attribute] ?? null;
    }

    /**
     * Scope: Filter by attribute value
     */
    public function scopeWithAttribute($query, $attribute, $value)
    {
        return $query->whereJsonContains('variant_combination->' . $attribute, $value);
    }

    /**
     * Scope: Filter by multiple attributes
     */
    public function scopeWithAttributes($query, array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $query->whereJsonContains('variant_combination->' . $key, $value);
        }
        return $query;
    }

    /**
     * Get available stock (total stock minus reserved)
     */
    public function getAvailableStockAttribute()
    {
        return max(0, $this->stock - ($this->reserved_stock ?? 0));
    }

    /**
     * Check if variant is in stock
     */
    public function getIsInStockAttribute()
    {
        return $this->available_stock > 0;
    }

    /**
     * Reserve stock for pending orders
     */
    public function reserveStock($quantity)
    {
        if ($this->available_stock >= $quantity) {
            $this->increment('reserved_stock', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Release reserved stock back to available
     */
    public function releaseReservedStock($quantity)
    {
        $releaseAmount = min($quantity, $this->reserved_stock ?? 0);
        if ($releaseAmount > 0) {
            $this->decrement('reserved_stock', $releaseAmount);
        }
        return $releaseAmount;
    }

    /**
     * Deduct stock when payment is confirmed
     */
    public function deductStock($quantity)
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            $this->increment('sold_count', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Check if has enough stock to reserve
     */
    public function canReserveStock($quantity)
    {
        return $this->available_stock >= $quantity;
    }

    /**
     * Uncomment this if you want slug-based routing for individual variants
     */
    // public function getRouteKeyName()
    // {
    //     return 'slug';
    // }

    // Your existing relationships remain the same
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'product_variant_reference');
    }

    public function cart_items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function item_shipment_statuses()
    {
        return $this->hasMany(ItemShipmentStatus::class);
    }

    public function paid_item_bystores()
    {
        return $this->hasMany(PaidItemBystore::class, 'product_id');
    }

    public function product_images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function product_reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_variants_id');
    }

    public function scopeInStock($query)
        {
            return $query->where('stock', '>', 0);
        }

        public function scopeAvailable($query)
        {
            return $query->whereRaw('(stock - COALESCE(reserved_stock, 0)) > 0');
        }
}
