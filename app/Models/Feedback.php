<?php

namespace App\Models;

use Database\Factories\FeedbackFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    /** @use HasFactory<FeedbackFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'body',
        'recipient_role',
        'recipient_college_id',
        'recipient_program_id',
        'escalated_from_id',
        'status',
        'read_at',
        'resolved_at',
        'resolution_note',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Feedback, $this>
     */
    public function escalatedFrom(): BelongsTo
    {
        return $this->belongsTo(Feedback::class, 'escalated_from_id');
    }
}
