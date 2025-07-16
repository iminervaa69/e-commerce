<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductReviewAttachment
 * 
 * @property int $id
 * @property int $review_id
 * @property string $attachment_type
 * @property string $file_url
 * @property string|null $file_name
 * @property int|null $file_size
 * @property Carbon|null $created_at
 * @property string|null $deleted_at
 * 
 * @property ProductReview $product_review
 *
 * @package App\Models
 */
class ProductReviewAttachment extends Model
{
	use SoftDeletes;
	protected $table = 'product_review_attachments';
	public $timestamps = false;

	protected $casts = [
		'review_id' => 'int',
		'file_size' => 'int'
	];

	protected $fillable = [
		'review_id',
		'attachment_type',
		'file_url',
		'file_name',
		'file_size'
	];

	public function product_review()
	{
		return $this->belongsTo(ProductReview::class, 'review_id');
	}
}
