<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shelf extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany<LearningResource, $this>
     */
    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(LearningResource::class, 'shelf_items', 'shelf_id', 'resource_id')
            ->withPivot(['note', 'created_at'])
            ->withTimestamps();
    }
}