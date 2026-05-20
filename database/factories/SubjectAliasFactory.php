<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\SubjectAlias;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubjectAlias>
 */
class SubjectAliasFactory extends Factory
{
    protected $model = SubjectAlias::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),
            'alias' => fake()->unique()->word(),
        ];
    }
}
