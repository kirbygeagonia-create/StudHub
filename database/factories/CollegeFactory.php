<?php

namespace Database\Factories;

use App\Models\College;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<College>
 */
class CollegeFactory extends Factory
{
    protected $model = College::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'code' => fake()->unique()->lexify('COL-????'),
            'name' => fake()->words(3, true),
        ];
    }
}
