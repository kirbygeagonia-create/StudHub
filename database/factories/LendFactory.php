<?php

namespace Database\Factories;

use App\Models\Lend;
use App\Models\LearningResource;
use App\Models\User;
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
        $fromUser = User::factory();
        $toUser = User::factory();

        return [
            'resource_id' => LearningResource::factory(),
            'from_user_id' => $fromUser,
            'to_user_id' => $toUser,
            'lent_at' => now(),
            'return_by' => now()->addDays(14)->format('Y-m-d'),
        ];
    }
}