<?php

namespace App\Models;

use App\Domain\Identity\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'school_id',
        'college_id',
        'program_id',
        'year_level',
        'name',
        'display_name',
        'avatar_url',
        'email',
        'password',
        'role',
        'karma',
        'last_seen_at',
        'onboarded_at',
        'suspended_until',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'onboarded_at' => 'datetime',
            'suspended_until' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'year_level' => 'integer',
            'karma' => 'integer',
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
     * @return BelongsTo<College, $this>
     */
    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    /**
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @return BelongsToMany<ChatRoom, $this>
     */
    public function chatRooms(): BelongsToMany
    {
        return $this->belongsToMany(ChatRoom::class, 'chat_room_memberships')
            ->withPivot(['joined_at', 'last_read_at', 'is_muted'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<ChatMessage, $this>
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    /**
     * @return HasMany<Shelf, $this>
     */
    public function shelves(): HasMany
    {
        return $this->hasMany(Shelf::class);
    }

    /**
     * @return HasMany<Request, $this>
     */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class, 'requester_user_id');
    }

    /**
     * @return HasMany<Offer, $this>
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'offerer_user_id');
    }

    /**
     * @return HasManyThrough<LearningResource, ShelfItem, $this>
     */
    public function savedResources(): HasManyThrough
    {
        return $this->hasManyThrough(
            LearningResource::class,
            ShelfItem::class,
            'shelf_id',
            'id',
            'id',
            'resource_id'
        );
    }

    public function hasCompletedOnboarding(): bool
    {
        return $this->onboarded_at !== null
            && $this->program_id !== null
            && $this->year_level !== null;
    }

    public function preferredDisplayName(): string
    {
        return $this->display_name ?: $this->name;
    }

    public function isStudent(): bool
    {
        return $this->role === UserRole::Student;
    }

    public function isModerator(): bool
    {
        return $this->role === UserRole::Moderator;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isSuspended(): bool
    {
        if ($this->suspended_until === null) {
            return false;
        }

        /** @var Carbon $date */
        $date = $this->suspended_until;

        return $date->isFuture();
    }
}
