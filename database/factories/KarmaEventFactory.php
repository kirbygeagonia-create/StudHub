<?php

namespace Database\Factories;

use App\Models\KarmaEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KarmaEvent>
 */
class KarmaEventFactory extends Factory
{
    protected $model = KarmaEvent::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'delta' => 1,
            'reason' => 'resource_uploaded',
        ];
    }
}
