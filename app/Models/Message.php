<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Message
 * 
 * @property int $id
 * @property int|null $chat_id
 * @property int|null $sender_id
 * @property int|null $recipient_id
 * @property string $encrypted_message
 * @property string $encrypted_symmetric_key
 * @property Carbon $sent_at
 * 
 * @property Chat|null $chat
 * @property User|null $user
 * @property Collection|Attachment[] $attachments
 *
 * @package App\Models
 */
class Message extends Model
{
	protected $table = 'messages';
	public $timestamps = false;

	protected $casts = [
		'chat_id' => 'int',
		'sender_id' => 'int',
		'recipient_id' => 'int',
		'sent_at' => 'datetime'
	];

	protected $fillable = [
		'chat_id',
		'sender_id',
		'recipient_id',
		'encrypted_message',
		'encrypted_symmetric_key',
		'sent_at'
	];

	public function chat()
	{
		return $this->belongsTo(Chat::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'recipient_id');
	}

	public function attachments()
	{
		return $this->hasMany(Attachment::class);
	}
}
