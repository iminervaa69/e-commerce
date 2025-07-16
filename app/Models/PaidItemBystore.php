<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaidItemBystore
 * 
 * @property int $id
 * @property int $product_id
 * @property int $paid_item_bystore_id
 * @property int $quantity
 * @property int $total_paid
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property ProductVariant $product_variant
 * @property PaidBystore $paid_bystore
 * @property Collection|ProductReview[] $product_reviews
 *
 * @package App\Models
 */
class PaidItemBystore extends Model
{
	use SoftDeletes;
	protected $table = 'paid_item_bystore';

	protected $casts = [
		'product_id' => 'int',
		'paid_item_bystore_id' => 'int',
		'quantity' => 'int',
		'total_paid' => 'int'
	];

	protected $fillable = [
		'product_id',
		'paid_item_bystore_id',
		'quantity',
		'total_paid'
	];

	public function product_variant()
	{
		return $this->belongsTo(ProductVariant::class, 'product_id');
	}

	public function paid_bystore()
	{
		return $this->belongsTo(PaidBystore::class, 'paid_item_bystore_id');
	}

	public function product_reviews()
	{
		return $this->hasMany(ProductReview::class);
	}
}
