<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'store_id',
        'order_number',
        'subtotal',
        'shipping_cost',
        'tax_amount',
        'total_amount',
        'status',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $dates = [
        'confirmed_at',
        'cancelled_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Alternative relationship name for flexibility (as used in your controller)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled'
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getIsConfirmedAttribute()
    {
        return $this->status === 'confirmed';
    }

    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled';
    }

    // Calculate total items in order
    public function getTotalItemsAttribute()
    {
        return $this->orderItems->sum('quantity');
    }
}
