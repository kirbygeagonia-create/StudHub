<?php

namespace App\Models;

use App\Domain\Reputation\Enums\Badge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'badge',
        'earned_at',
    ];

    protected function casts(): array
    {
        return [
            'badge' => Badge::class,
            'earned_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
