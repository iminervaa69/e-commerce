<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'reference_id',
        'address_id',
        'billing_information_id',
        'xendit_id',
        'amount',
        'total_amount',
        'currency',
        'payment_method',
        'customer_name',
        'customer_email',
        'customer_phone',
        'status',
        'paid_at',
        'failed_at',
        'failure_reason',
        'paid_amount',
        'xendit_response',
        'webhook_data'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'xendit_response' => 'array',
        'webhook_data' => 'array',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];


    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'expired']);
    }

    // Accessors & Mutators
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getIsPaidAttribute()
    {
        return $this->status === 'paid';
    }

    public function getIsFailedAttribute()
    {
        return in_array($this->status, ['failed', 'expired', 'cancelled']);
    }

    public function address() {
    return $this->belongsTo(Address::class);
    }

    public function billingInformation() {
        return $this->belongsTo(BillingInformation::class);
    }
}
