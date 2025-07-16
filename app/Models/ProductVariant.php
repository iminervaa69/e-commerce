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
 * Class ProductVariant
 * 
 * @property int $id
 * @property string $name
 * @property int $product_id
 * @property string|null $description
 * @property float $price
 * @property int $stock
 * @property string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Product $product
 * @property Collection|Attachment[] $attachments
 * @property Collection|CartItem[] $cart_items
 * @property Collection|ItemShipmentStatus[] $item_shipment_statuses
 * @property Collection|PaidItemBystore[] $paid_item_bystores
 * @property Collection|ProductReview[] $product_reviews
 *
 * @package App\Models
 */
class ProductVariant extends Model
{
	use SoftDeletes;
	protected $table = 'product_variants';

	protected $casts = [
		'product_id' => 'int',
		'price' => 'float',
		'stock' => 'int'
	];

	protected $fillable = [
		'name',
		'product_id',
		'description',
		'price',
		'stock',
		'status'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function attachments()
	{
		return $this->hasMany(Attachment::class, 'product_variant_reference');
	}

	public function cart_items()
	{
		return $this->hasMany(CartItem::class);
	}

	public function item_shipment_statuses()
	{
		return $this->hasMany(ItemShipmentStatus::class);
	}

	public function paid_item_bystores()
	{
		return $this->hasMany(PaidItemBystore::class, 'product_id');
	}

	public function product_reviews()
	{
		return $this->hasMany(ProductReview::class, 'product_variants_id');
	}
}
