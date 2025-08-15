<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
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
class User extends Authenticatable // Changed from Model to Authenticatable
{
    use HasFactory, Notifiable; // Add these traits

    protected $table = 'users';

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Add this for automatic password hashing
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token'
    ];

    // Your existing relationships
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

    // Helper method to check if user has a specific role in any store
    public function hasStoreRole($role = null)
    {
        if ($role) {
            return $this->stores()->wherePivot('role', $role)->exists();
        }
        return $this->stores()->exists();
    }

    // Helper method to get user's stores by role
    public function storesByRole($role)
    {
        return $this->stores()->wherePivot('role', $role)->get();
    }
}
