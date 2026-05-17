<?php

namespace Database\Factories;

use App\Models\Lend;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lend>
 */
class LendFactory extends Factory
{
    protected $model = Lend::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'resource_id' => null,
            'from_user_id' => null,
            'to_user_id' => null,
            'lent_at' => now(),
            'return_by' => now()->addDays(14)->format('Y-m-d'),
        ];
    }
}