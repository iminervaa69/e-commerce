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
 * Class Store
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $description
 * @property string|null $status
 * @property int $day_of_week
 * @property Carbon $open_time
 * @property Carbon $close_time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|PaidBystore[] $paid_bystores
 * @property Collection|Product[] $products
 * @property Collection|StoreActionLog[] $store_action_logs
 * @property Collection|StoreStatus[] $store_statuses
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Store extends Model
{
	use SoftDeletes;
	protected $table = 'stores';

	protected $casts = [
		'day_of_week' => 'int',
		'open_time' => 'datetime',
		'close_time' => 'datetime'
	];

	protected $fillable = [
		'name',
		'email',
		'address',
		'phone',
		'description',
		'status',
		'day_of_week',
		'open_time',
		'close_time'
	];

	public function paid_bystores()
	{
		return $this->hasMany(PaidBystore::class);
	}

	public function products()
	{
		return $this->hasMany(Product::class);
	}

	public function store_action_logs()
	{
		return $this->hasMany(StoreActionLog::class);
	}

	public function store_statuses()
	{
		return $this->hasMany(StoreStatus::class);
	}

	public function users()
	{
		return $this->belongsToMany(User::class, 'store_user_roles')
					->withPivot('id', 'role', 'deleted_at')
					->withTimestamps();
	}
}
