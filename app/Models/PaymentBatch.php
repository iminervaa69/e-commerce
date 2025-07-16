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
 * Class PaymentBatch
 * 
 * @property int $id
 * @property int $user_id
 * @property Carbon $payment_date
 * @property string $payment_method
 * @property float $payment_difference
 * @property string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 * @property Collection|PaidBystore[] $paid_bystores
 *
 * @package App\Models
 */
class PaymentBatch extends Model
{
	use SoftDeletes;
	protected $table = 'payment_batchs';

	protected $casts = [
		'user_id' => 'int',
		'payment_date' => 'datetime',
		'payment_difference' => 'float'
	];

	protected $fillable = [
		'user_id',
		'payment_date',
		'payment_method',
		'payment_difference',
		'status'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function paid_bystores()
	{
		return $this->hasMany(PaidBystore::class);
	}
}
