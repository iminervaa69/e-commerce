<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'province',
        'city',
        'district',
        'postal_code',
        'street_address',
        'address_notes',
        'is_default',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $appends = [
        'full_address',
        'formatted_address'
    ];

    /**
     * Get the user that owns the address
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full address as a single string
     */
    public function getFullAddressAttribute()
    {
        $parts = [
            $this->street_address,
            $this->district,
            $this->city,
            $this->province,
            $this->postal_code
        ];

        return implode(', ', array_filter($parts));
    }

    /**
     * Get formatted address for display (with recipient info)
     */
    public function getFormattedAddressAttribute()
    {
        $address = $this->recipient_name . "\n";
        $address .= $this->phone . "\n";
        $address .= $this->full_address;
        
        if ($this->address_notes) {
            $address .= "\nNote: " . $this->address_notes;
        }

        return $address;
    }

    /**
     * Scope to get default address
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get addresses by label
     */
    public function scopeByLabel($query, $label)
    {
        return $query->where('label', $label);
    }

    /**
     * Get the short address (street + district)
     */
    public function getShortAddressAttribute()
    {
        return $this->street_address . ', ' . $this->district;
    }

    /**
     * Check if this is the user's only address
     */
    public function isOnlyAddress()
    {
        return $this->user->addresses()->count() === 1;
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Ensure only one default address per user
        static::saving(function ($address) {
            if ($address->is_default) {
                // Set all other addresses for this user to non-default
                static::where('user_id', $address->user_id)
                      ->where('id', '!=', $address->id ?? 0)
                      ->update(['is_default' => false]);
            }
        });

        // If deleting the default address, set another as default
        static::deleting(function ($address) {
            if ($address->is_default) {
                $nextAddress = static::where('user_id', $address->user_id)
                                    ->where('id', '!=', $address->id)
                                    ->first();
                
                if ($nextAddress) {
                    $nextAddress->update(['is_default' => true]);
                }
            }
        });
    }
}