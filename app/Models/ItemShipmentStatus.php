<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ItemShipmentStatus
 * 
 * @property int $id
 * @property int $product_variant_id
 * @property string $shipment_status
 * @property string|null $note
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property ProductVariant $product_variant
 *
 * @package App\Models
 */
class ItemShipmentStatus extends Model
{
	use SoftDeletes;
	protected $table = 'item_shipment_status';

	protected $casts = [
		'product_variant_id' => 'int'
	];

	protected $fillable = [
		'product_variant_id',
		'shipment_status',
		'note'
	];

	public function product_variant()
	{
		return $this->belongsTo(ProductVariant::class);
	}
}
