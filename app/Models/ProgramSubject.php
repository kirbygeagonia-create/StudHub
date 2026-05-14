<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProgramSubject extends Pivot
{
    protected $table = 'program_subjects';

    public $incrementing = false;

    protected $fillable = [
        'program_id',
        'subject_id',
        'typical_year_level',
        'weight',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'typical_year_level' => 'integer',
            'weight' => 'float',
        ];
    }
}
