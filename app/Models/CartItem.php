<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CartItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_variant_id',
        'quantity',
        'price_when_added',
        'expires_at'
    ];

    protected $casts = [
        'price_when_added' => 'decimal:2',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method to set defaults when creating
     */
    protected static function booted()
    {
        static::creating(function ($cartItem) {
            // Set price snapshot if not provided
            if (!$cartItem->price_when_added && $cartItem->productVariant) {
                $cartItem->price_when_added = $cartItem->productVariant->price;
            }
            
            // Set expiration based on user type
            if (!$cartItem->expires_at) {
                if ($cartItem->user_id) {
                    // Logged-in users: 30 days
                    $cartItem->expires_at = Carbon::now()->addDays(30);
                } else {
                    // Guest users: 24 hours
                    $cartItem->expires_at = Carbon::now()->addHours(24);
                }
            }
        });

        // Extend expiration when updating
        static::updating(function ($cartItem) {
            if ($cartItem->isDirty('quantity')) {
                if ($cartItem->user_id) {
                    $cartItem->expires_at = Carbon::now()->addDays(30);
                } else {
                    $cartItem->expires_at = Carbon::now()->addHours(24);
                }
            }
        });
    }

    // ===== RELATIONSHIPS =====
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // ===== SCOPES =====
    
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', Carbon::now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId)->whereNull('user_id');
    }

    public function scopeGuest($query)
    {
        return $query->whereNull('user_id')->whereNotNull('session_id');
    }

    // ===== HELPER METHODS =====
    
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function extendExpiration(int $days = null): self
    {
        $days = $days ?? ($this->user_id ? 30 : 1);
        $this->update(['expires_at' => Carbon::now()->addDays($days)]);
        return $this;
    }

    public function getTotalPrice(): float
    {
        return (float) ($this->price_when_added * $this->quantity);
    }

    public function hasCurrentPriceChanged(): bool
    {
        return $this->productVariant && 
               $this->price_when_added != $this->productVariant->price;
    }

    public function getCurrentPriceDifference(): float
    {
        if (!$this->productVariant) return 0;
        return (float) ($this->productVariant->price - $this->price_when_added);
    }

    public function isGuest(): bool
    {
        return is_null($this->user_id) && !is_null($this->session_id);
    }

    public function isAuthenticated(): bool
    {
        return !is_null($this->user_id);
    }
}