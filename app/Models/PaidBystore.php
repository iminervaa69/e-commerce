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
 * Class PaidBystore
 * 
 * @property int $id
 * @property int $store_id
 * @property int $payment_batch_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Store $store
 * @property PaymentBatch $payment_batch
 * @property Collection|PaidItemBystore[] $paid_item_bystores
 *
 * @package App\Models
 */
class PaidBystore extends Model
{
	use SoftDeletes;
	protected $table = 'paid_bystore';

	protected $casts = [
		'store_id' => 'int',
		'payment_batch_id' => 'int'
	];

	protected $fillable = [
		'store_id',
		'payment_batch_id'
	];

	public function store()
	{
		return $this->belongsTo(Store::class);
	}

	public function payment_batch()
	{
		return $this->belongsTo(PaymentBatch::class);
	}

	public function paid_item_bystores()
	{
		return $this->hasMany(PaidItemBystore::class, 'paid_item_bystore_id');
	}
}
