<?php

namespace App\Models;

use Database\Factories\SubjectAliasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectAlias extends Model
{
    /** @use HasFactory<SubjectAliasFactory> */
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'alias',
    ];

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
