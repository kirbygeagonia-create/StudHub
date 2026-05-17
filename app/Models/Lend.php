<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lend extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'from_user_id',
        'to_user_id',
        'lent_at',
        'return_by',
        'returned_at',
        'condition_on_return',
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
}