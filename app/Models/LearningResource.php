<?php

namespace App\Models;

use App\Domain\Catalog\Enums\ResourceAvailability;
use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Catalog\Enums\ResourceVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class LearningResource extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'resources';

    protected $fillable = [
        'school_id',
        'owner_user_id',
        'subject_id',
        'program_id',
        'type',
        'title',
        'description',
        'course_code',
        'year_taken',
        'year_level',
        'condition',
        'availability',
        'visibility',
        'file_url',
        'file_mime',
        'file_size',
        'is_watermarked',
        'save_count',
        'lend_count',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ResourceType::class,
            'availability' => ResourceAvailability::class,
            'visibility' => ResourceVisibility::class,
            'year_taken' => 'integer',
            'year_level' => 'integer',
            'file_size' => 'integer',
            'save_count' => 'integer',
            'lend_count' => 'integer',
            'is_watermarked' => 'boolean',
            'published_at' => 'datetime',
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
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function isProgramOnly(): bool
    {
        $value = $this->getAttribute('visibility');

        if ($value instanceof ResourceVisibility) {
            return $value === ResourceVisibility::ProgramOnly;
        }

        return (string) $value === ResourceVisibility::ProgramOnly->value;
    }

    /**
     * @param  Builder<LearningResource>  $query
     * @return Builder<LearningResource>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $driver = DB::connection($query->getModel()->getConnectionName())->getDriverName();
        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $term) . '%';

        if ($driver === 'mysql') {
            return $query->where(function (Builder $q) use ($term, $like): void {
                $q->whereRaw('MATCH(title, description) AGAINST (? IN BOOLEAN MODE)', [$term . '*'])
                    ->orWhere('title', 'like', $like);
            });
        }

        return $query->where(function (Builder $q) use ($like): void {
            $q->where('title', 'like', $like)
                ->orWhere('description', 'like', $like);
        });
    }
}
