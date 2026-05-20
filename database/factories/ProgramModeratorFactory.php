<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\ProgramModerator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgramModerator>
 */
class ProgramModeratorFactory extends Factory
{
    protected $model = ProgramModerator::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'program_id' => Program::factory(),
            'assigned_by_user_id' => User::factory(),
        ];
    }
}
