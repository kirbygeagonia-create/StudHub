<?php

namespace App\Models;

use App\Domain\Chat\Enums\ChatRoomKind;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'kind',
        'program_id',
        'year_level',
        'request_id',
        'title',
        'slug',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kind' => ChatRoomKind::class,
            'year_level' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @return HasMany<ChatMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_room_memberships')
            ->withPivot(['joined_at', 'last_read_at', 'is_muted'])
            ->withTimestamps();
    }

    /**
     * The Reverb / broadcasting channel name for this room. Aligns with the
     * matchers registered in `routes/channels.php`.
     */
    public function broadcastChannel(): string
    {
        return 'chat-room.' . $this->id;
    }
}
