<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Attachment
 * 
 * @property int $id
 * @property int $message_id
 * @property string $attachment_type
 * @property string $file_url
 * @property string|null $file_name
 * @property int|null $file_size
 * @property int|null $product_variant_reference
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Message $message
 * @property ProductVariant|null $product_variant
 *
 * @package App\Models
 */
class Attachment extends Model
{
	use SoftDeletes;
	protected $table = 'attachments';

	protected $casts = [
		'message_id' => 'int',
		'file_size' => 'int',
		'product_variant_reference' => 'int'
	];

	protected $fillable = [
		'message_id',
		'attachment_type',
		'file_url',
		'file_name',
		'file_size',
		'product_variant_reference'
	];

	public function message()
	{
		return $this->belongsTo(Message::class);
	}

	public function product_variant()
	{
		return $this->belongsTo(ProductVariant::class, 'product_variant_reference');
	}
}
