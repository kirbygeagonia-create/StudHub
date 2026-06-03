<?php

namespace App\Models;

use App\Domain\Lends\Enums\LendCondition;
use App\Domain\Lends\Notifications\LendEscalated;
use Database\Factories\LendFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method Builder dueSoon(int $days = 2)
 */
class Lend extends Model
{
    /** @use HasFactory<LendFactory> */
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'offer_id',
        'request_id',
        'from_user_id',
        'to_user_id',
        'lent_at',
        'return_by',
        'returned_at',
        'condition_on_return',
        'reminder_count',
        'escalated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lent_at' => 'datetime',
            'returned_at' => 'datetime',
            'return_by' => 'date',
            'condition_on_return' => LendCondition::class,
            'reminder_count' => 'integer',
            'escalated_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<LearningResource, $this>
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(LearningResource::class);
    }

    /**
     * @return BelongsTo<Offer, $this>
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * @return BelongsTo<ResourceRequest, $this>
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(ResourceRequest::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function isReturned(): bool
    {
        return $this->returned_at !== null;
    }

    public function isOverdue(): bool
    {
        if ($this->isReturned() || $this->return_by === null) {
            return false;
        }

        return now()->startOfDay()->gt($this->return_by);
    }

    public function isDueSoon(int $days = 2): bool
    {
        if ($this->isReturned() || $this->return_by === null) {
            return false;
        }

        $dueIn = (int) now()->startOfDay()->diffInDays($this->return_by, false);

        return $dueIn >= 0 && $dueIn <= $days;
    }

    public function isEscalated(): bool
    {
        return $this->escalated_at !== null;
    }

    public function needsEscalation(): bool
    {
        return ! $this->isReturned()
            && ! $this->isEscalated()
            && ($this->reminder_count ?? 0) >= 3;
    }

    public function escalate(): void
    {
        if ($this->needsEscalation()) {
            $this->escalated_at = now();
            $this->save();

            if ($this->toUser !== null) {
                $this->toUser->notify(new LendEscalated($this));
            }
        }
    }

    /**
     * Scope lends that need a return reminder (due within N days, not returned, has a return_by date).
     *
     * @param  Builder<Lend>  $query
     * @return Builder<Lend>
     */
    public function scopeDueSoon(Builder $query, int $days = 2): Builder
    {
        $from = now()->startOfDay()->format('Y-m-d');
        $to = now()->startOfDay()->addDays($days)->format('Y-m-d');

        return $query->whereNull('returned_at')
            ->whereNotNull('return_by')
            ->whereBetween('return_by', [$from, $to]);
    }
}
