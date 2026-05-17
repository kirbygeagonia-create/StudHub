<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<School>
 */
class SchoolFactory extends Factory
{
    protected $model = School::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->lexify('SCHOOL-????'),
            'name' => fake()->company(),
            'short_name' => fake()->lexify('???'),
            'timezone' => 'Asia/Manila',
            'email_domains' => ['school.edu.ph'],
            'location' => fake()->city(),
        ];
    }
}