<?php

namespace App\Models;

use App\Domain\Identity\Enums\UserRole;
use App\Domain\Reputation\Enums\BadgeTier;
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
        'karma',
        'last_seen_at',
        'onboarded_at',
        'suspended_until',
        'student_number',
        'notification_preferences',
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
            'notification_preferences' => 'array',
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
            ->withPivot(['joined_at', 'last_read_at', 'is_muted', 'unread_count'])
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
     * @return HasMany<LearningResource, $this>
     */
    public function resources(): HasMany
    {
        return $this->hasMany(LearningResource::class, 'owner_user_id');
    }

    /**
     * @return HasMany<ResourceRequest, $this>
     */
    public function requests(): HasMany
    {
        return $this->hasMany(ResourceRequest::class, 'requester_user_id');
    }

    /**
     * @return HasMany<Offer, $this>
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'offerer_user_id');
    }

    /**
     * @return HasMany<UserBadge, $this>
     */
    public function badges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * Returns the current karma-based tier.
     */
    public function currentTier(): BadgeTier
    {
        return BadgeTier::fromKarma($this->karma ?? 0);
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
        // SAO and SuperAdmin: campus-wide scope — only need school_id + display_name
        if ($this->isSao() || $this->isSuperAdmin()) {
            return $this->onboarded_at !== null
                && $this->display_name !== null
                && $this->school_id !== null;
        }

        // Dean and Program Head: college-scoped — need college_id but NOT program_id or year_level
        if ($this->isDean() || $this->isProgramHead()) {
            return $this->onboarded_at !== null
                && $this->display_name !== null
                && $this->school_id !== null
                && $this->college_id !== null;
        }

        // Students and Moderators: need full profile including program + year
        return $this->onboarded_at !== null
            && $this->program_id !== null
            && $this->year_level !== null
            && $this->display_name !== null
            && $this->school_id !== null
            && $this->college_id !== null;
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

    public function isProgramHead(): bool
    {
        return $this->role === UserRole::ProgramHead;
    }

    public function isDean(): bool
    {
        return $this->role === UserRole::Dean;
    }

    public function isSao(): bool
    {
        return $this->role === UserRole::Sao;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [
            UserRole::ProgramHead,
            UserRole::Dean,
            UserRole::Sao,
            UserRole::SuperAdmin,
        ]);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isSystem(): bool
    {
        return $this->role === UserRole::System;
    }

    /**
     * Returns all role values this user has permissions for (inheritance).
     *
     * @return array<int, string>
     */
    public function inheritedRoles(): array
    {
        return $this->role instanceof UserRole
            ? $this->role->inheritedRoles()
            : ['student'];
    }

    /**
     * Returns the program_id this user manages (null if they manage all).
     * Program Head manages an entire college — not one specific program.
     */
    public function managedProgramId(): ?int
    {
        return null;
    }

    /**
     * Returns the college_id this user manages (null if they manage all).
     */
    public function managedCollegeId(): ?int
    {
        return ($this->isDean() || $this->isProgramHead())
            ? $this->college_id
            : null;
    }

    /**
     * CSS panel class for role-aware UI.
     */
    public function panelClass(): string
    {
        return $this->role instanceof UserRole
            ? $this->role->panelClass()
            : '';
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
