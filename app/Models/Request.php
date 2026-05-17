<?php

namespace App\Models;

use App\Domain\Requests\Enums\RequestStatus;
use App\Domain\Requests\Enums\RequestUrgency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Request extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'requester_user_id',
        'subject_id',
        'type_wanted',
        'urgency',
        'needed_by',
        'description',
        'status',
        'fulfilled_offer_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'urgency' => RequestUrgency::class,
            'status' => RequestStatus::class,
            'needed_by' => 'date',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return HasMany<RequestRoute, $this>
     */
    public function routes(): HasMany
    {
        return $this->hasMany(RequestRoute::class);
    }

    /**
     * @return HasMany<Offer, $this>
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * @return BelongsTo<Offer, $this>
     */
    public function fulfilledOffer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'fulfilled_offer_id');
    }

    public function isOpen(): bool
    {
        return $this->status === RequestStatus::Open;
    }
}