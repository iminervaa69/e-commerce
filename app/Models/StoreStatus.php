<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class StoreStatus
 * 
 * @property int $id
 * @property int $store_id
 * @property string|null $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Store $store
 *
 * @package App\Models
 */
class StoreStatus extends Model
{
	use SoftDeletes;
	protected $table = 'store_statuses';

	protected $casts = [
		'store_id' => 'int'
	];

	protected $fillable = [
		'store_id',
		'status'
	];

	public function store()
	{
		return $this->belongsTo(Store::class);
	}
}
