<?php

namespace Database\Factories;

use App\Domain\Moderation\Enums\ReportStatus;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reporter_user_id' => User::factory(),
            'reported_type' => 'resource',
            'reported_id' => null,
            'reason' => fake()->word(),
            'notes' => fake()->optional()->sentence(),
            'status' => ReportStatus::Open->value,
        ];
    }
}
