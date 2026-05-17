<?php

namespace App\Models;

use App\Domain\Reputation\Enums\KarmaEventReason;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KarmaEvent extends Model
{
    use HasFactory;

    protected $table = 'karma_events';

    protected $fillable = [
        'user_id',
        'delta',
        'reason',
        'related_type',
        'related_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'delta' => 'integer',
            'related_id' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}