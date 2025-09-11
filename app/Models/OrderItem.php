<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'total_price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // Alternative relationship name for flexibility (as used in your controller)
    public function product_variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Accessors
    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 2);
    }

    public function getFormattedTotalPriceAttribute()
    {
        return number_format($this->total_price, 2);
    }

    // Mutators - automatically calculate total_price when quantity or unit_price changes
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = $value;
        $this->calculateTotalPrice();
    }

    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price'] = $value;
        $this->calculateTotalPrice();
    }

    private function calculateTotalPrice()
    {
        if (isset($this->attributes['quantity']) && isset($this->attributes['unit_price'])) {
            $this->attributes['total_price'] = $this->attributes['quantity'] * $this->attributes['unit_price'];
        }
    }

    // Get product name through variant
    public function getProductNameAttribute()
    {
        return $this->productVariant->product->name ?? 'Unknown Product';
    }

    // Get variant name
    public function getVariantNameAttribute()
    {
        return $this->productVariant->name ?? 'Default Variant';
    }
}
