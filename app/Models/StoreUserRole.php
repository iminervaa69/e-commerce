<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class StoreUserRole
 * 
 * @property int $id
 * @property int $store_id
 * @property int $user_id
 * @property string $role
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Store $store
 * @property User $user
 *
 * @package App\Models
 */
class StoreUserRole extends Model
{
	use SoftDeletes;
	protected $table = 'store_user_roles';

	protected $casts = [
		'store_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'store_id',
		'user_id',
		'role'
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
