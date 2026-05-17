<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\RequestRoute;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'college_id',
        'code',
        'name',
        'default_year_levels',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_year_levels' => 'integer',
            'is_active' => 'boolean',
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
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<ChatRoom, $this>
     */
    public function chatRooms(): HasMany
    {
        return $this->hasMany(ChatRoom::class);
    }

    /**
     * @return BelongsToMany<Subject, $this>
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'program_subjects')
            ->using(ProgramSubject::class)
            ->withPivot(['typical_year_level', 'weight'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<LearningResource, $this>
     */
    public function resources(): HasMany
    {
        return $this->hasMany(LearningResource::class);
    }

    /**
     * @return HasMany<RequestRoute, $this>
     */
    public function requestRoutes(): HasMany
    {
        return $this->hasMany(RequestRoute::class);
    }
}
