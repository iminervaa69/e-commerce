<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CartItem
 * 
 * @property int $id
 * @property int $user_id
 * @property int $product_variant_id
 * @property int $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 * @property ProductVariant $product_variant
 *
 * @package App\Models
 */
class CartItem extends Model
{
	use SoftDeletes;
	protected $table = 'cart_items';

	protected $casts = [
		'user_id' => 'int',
		'product_variant_id' => 'int',
		'quantity' => 'int'
	];

	protected $fillable = [
		'user_id',
		'product_variant_id',
		'quantity'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function product_variant()
	{
		return $this->belongsTo(ProductVariant::class);
	}
}
