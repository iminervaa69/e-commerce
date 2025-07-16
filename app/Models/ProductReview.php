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
 * Class ProductReview
 * 
 * @property int $id
 * @property int $product_id
 * @property int $product_variants_id
 * @property int $user_id
 * @property int $paid_item_bystore_id
 * @property int $rating
 * @property string|null $review_text
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Product $product
 * @property ProductVariant $product_variant
 * @property User $user
 * @property PaidItemBystore $paid_item_bystore
 * @property Collection|ProductReviewAttachment[] $product_review_attachments
 *
 * @package App\Models
 */
class ProductReview extends Model
{
	use SoftDeletes;
	protected $table = 'product_reviews';

	protected $casts = [
		'product_id' => 'int',
		'product_variants_id' => 'int',
		'user_id' => 'int',
		'paid_item_bystore_id' => 'int',
		'rating' => 'int'
	];

	protected $fillable = [
		'product_id',
		'product_variants_id',
		'user_id',
		'paid_item_bystore_id',
		'rating',
		'review_text'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function product_variant()
	{
		return $this->belongsTo(ProductVariant::class, 'product_variants_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function paid_item_bystore()
	{
		return $this->belongsTo(PaidItemBystore::class);
	}

	public function product_review_attachments()
	{
		return $this->hasMany(ProductReviewAttachment::class, 'review_id');
	}
}
