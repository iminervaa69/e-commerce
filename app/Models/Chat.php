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
 * Class Chat
 * 
 * @property int $id
 * @property int|null $buyer_id
 * @property int|null $seller_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User|null $user
 * @property Collection|Message[] $messages
 *
 * @package App\Models
 */
class Chat extends Model
{
	use SoftDeletes;
	protected $table = 'chat';

	protected $casts = [
		'buyer_id' => 'int',
		'seller_id' => 'int'
	];

	protected $fillable = [
		'buyer_id',
		'seller_id'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'seller_id');
	}

	public function messages()
	{
		return $this->hasMany(Message::class);
	}
}
