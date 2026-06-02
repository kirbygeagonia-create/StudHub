<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceHelpfulVote extends Model
{
    protected $fillable = [
        'resource_id',
        'user_id',
    ];

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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
