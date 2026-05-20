<?php

namespace Database\Factories;

use App\Models\College;
use App\Models\Program;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'college_id' => College::factory(),
            'code' => fake()->unique()->lexify('BS???'),
            'name' => fake()->words(3, true),
            'default_year_levels' => 4,
            'is_active' => true,
        ];
    }
}
