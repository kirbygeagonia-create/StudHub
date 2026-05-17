<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ShelfItem extends Pivot
{
    protected $table = 'shelf_items';

    protected $fillable = [
        'shelf_id',
        'resource_id',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}