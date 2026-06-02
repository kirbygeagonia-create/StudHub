<?php

namespace App\Models;

use Database\Factories\ChatMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    /** @use HasFactory<ChatMessageFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chat_room_id',
        'sender_id',
        'body',
        'is_system',
        'attachment_url',
        'attachment_mime',
        'attachment_size',
        'attachment_name',
        'reply_to_message_id',
        'is_helpful',
        'marked_helpful_by_user_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attachment_size' => 'integer',
            'is_helpful' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<ChatRoom, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * @return BelongsTo<self, $this>
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reply_to_message_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function mentions(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_message_mentions')
            ->withTimestamps();
    }

    public function hasAttachment(): bool
    {
        return $this->attachment_url !== null;
    }
}
