<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Conversation
 *
 * @property int $id
 * @property int $sender_id
 * @property string $sender_type
 * @property int $receiver_id
 * @property string $receiver_type
 * @property int|null $last_message_id
 * @property Carbon|null $last_message_time
 * @property int $unread_message_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Conversation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id',
        'sender_type',
        'receiver_id',
        'receiver_type',
        'last_message_id',
        'last_message_time',
        'unread_message_count',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'sender_id' => 'integer',
        'receiver_id' => 'integer',
        'last_message_id' => 'integer',
        'unread_message_count' => 'integer',
        'order_id' => 'integer',
        'details_count' => 'integer',
        'order_amount' => 'float',

    ];

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }

    /**
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(UserInfo::class, 'sender_id');
    }

    /**
     * @return BelongsTo
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(UserInfo::class, 'receiver_id');
    }

    /**
     * @return BelongsTo
     */
    public function last_message(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    /**
     * @param $query
     * @param $user_id
     * @return void
     */
    public function scopeWhereUser($query, $user_id): void
    {
        $query->where(function($q)use($user_id){
            $q->where('sender_id',$user_id)->orWhere('receiver_id',$user_id);
        });
    }

    /**
     * @param $query
     * @param $sender_id
     * @param $receiver_id
     * @return void
     */
    public function scopeWhereConversation($query, $sender_id, $receiver_id): void
    {
        $query->where(function($q)use($sender_id, $receiver_id){
            $q->where('sender_id',$sender_id)->where('receiver_id',$receiver_id);
        })->orWhere(function($q)use($sender_id, $receiver_id){
            $q->where('sender_id',$receiver_id)->where('receiver_id',$sender_id);
        });
    }

    /**
     * @param $query
     * @param $type
     * @return void
     */
    public function scopeWhereUserType($query, $type): void
    {
        $query->where(function($q)use($type){
            $q->where('sender_type',$type)->orWhere('receiver_type',$type);
        });
    }
}
