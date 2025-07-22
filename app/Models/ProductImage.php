<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductImage
 * 
 * @property int $id
 * @property int $product_id
 * @property int|null $product_variant_id
 * @property string $image_url
 * @property bool|null $is_primary
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Product $product
 * @property ProductVariant|null $product_variant
 *
 * @package App\Models
 */
class ProductImage extends Model
{
	use SoftDeletes;
	protected $table = 'product_images';

	protected $casts = [
		'product_id' => 'int',
		'product_variant_id' => 'int',
		'is_primary' => 'bool'
	];

	protected $fillable = [
		'product_id',
		'product_variant_id',
		'image_url',
		'is_primary'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function product_variant()
	{
		return $this->belongsTo(ProductVariant::class);
	}

	public function getVariantNameAttribute()
	{
		return $this->product_variant->name ?? '—';
	}

	public function getIsPrimaryLabelAttribute()
	{
		return $this->is_primary ? 'Yes' : 'No';
	}
}
