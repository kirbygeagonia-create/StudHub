<?php

namespace App\Models;

use App\Domain\Moderation\Enums\ReportedType;
use App\Domain\Moderation\Enums\ReportStatus;
use App\Models\Scopes\ReportSchoolScope;
use Database\Factories\ReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    /** @use HasFactory<ReportFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new ReportSchoolScope);
    }

    protected $fillable = [
        'reporter_user_id',
        'reported_type',
        'reported_id',
        'reason',
        'notes',
        'status',
        'handled_by_user_id',
        'resolution_note',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'reported_type' => ReportedType::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function reported(): MorphTo
    {
        return $this->morphTo('reported');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_user_id');
    }

    public function isOpen(): bool
    {
        return $this->status === ReportStatus::Open;
    }
}
