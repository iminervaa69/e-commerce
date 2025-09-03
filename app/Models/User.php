<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|CartItem[] $cart_items
 * @property Collection|Chat[] $chats
 * @property Collection|Message[] $messages
 * @property Collection|PaymentBatch[] $payment_batches
 * @property Collection|ProductReview[] $product_reviews
 * @property Collection|StoreActionLog[] $store_action_logs
 * @property Collection|Store[] $stores
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $fillable = [
        'name',
        'slug',
        'email',
        'email_verified_at',
        'password',
        'remember_token'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            if (empty($user->slug)) {
                $user->slug = $user->generateUniqueSlug($user->name);
            }
        });

        static::updating(function ($user) {
            if ($user->isDirty('name') && empty($user->slug)) {
                $user->slug = $user->generateUniqueSlug($user->name);
            }
        });
    }

    /**
     * Generate a unique slug for the user
     */
    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the route key for the model (for public profiles)
     * You can comment this out if you don't want slug-based routing for users
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Your existing relationships and methods remain the same
    public function cart_items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'seller_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function payment_batches()
    {
        return $this->hasMany(PaymentBatch::class);
    }

    public function product_reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function store_action_logs()
    {
        return $this->hasMany(StoreActionLog::class);
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_user_roles')
                    ->withPivot('id', 'role', 'deleted_at')
                    ->withTimestamps();
    }

    public function hasStoreRole($role = null)
    {
        if ($role) {
            return $this->stores()->wherePivot('role', $role)->exists();
        }
        return $this->stores()->exists();
    }

    public function storesByRole($role)
    {
        return $this->stores()->wherePivot('role', $role)->get();
    }

        /**
     * Get the user's addresses
     */
    public function addresses()
    {
        return $this->hasMany(Address::class)->orderBy('is_default', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Get the user's default address
     */
    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function billingInformation()
    {
        return $this->hasMany(BillingInformation::class);
    }

    public function defaultBillingInformation()
    {
        return $this->hasOne(BillingInformation::class)->where('is_default', true);
    }
}
