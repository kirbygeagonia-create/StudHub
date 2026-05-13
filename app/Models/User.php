<?php

namespace App\Models;

use App\Domain\Identity\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
}
