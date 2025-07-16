<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StoreActionLog
 * 
 * @property int $id
 * @property int $store_id
 * @property int $user_id
 * @property string $action_type
 * @property string|null $target_table
 * @property int|null $target_id
 * @property string|null $detail
 * @property Carbon|null $created_at
 * 
 * @property Store $store
 * @property User $user
 *
 * @package App\Models
 */
class StoreActionLog extends Model
{
	protected $table = 'store_action_logs';
	public $timestamps = false;

	protected $casts = [
		'store_id' => 'int',
		'user_id' => 'int',
		'target_id' => 'int'
	];

	protected $fillable = [
		'store_id',
		'user_id',
		'action_type',
		'target_table',
		'target_id',
		'detail'
	];

	public function store()
	{
		return $this->belongsTo(Store::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
