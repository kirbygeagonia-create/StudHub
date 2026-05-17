<?php

namespace Database\Factories;

use App\Domain\Catalog\Enums\ResourceAvailability;
use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Catalog\Enums\ResourceVisibility;
use App\Models\LearningResource;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningResource>
 */
class LearningResourceFactory extends Factory
{
    protected $model = LearningResource::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'owner_user_id' => User::factory(),
            'subject_id' => null,
            'program_id' => null,
            'type' => ResourceType::Reviewer->value,
            'title' => fake()->words(4, true),
            'description' => fake()->optional()->sentence(),
            'availability' => ResourceAvailability::Available->value,
            'visibility' => ResourceVisibility::School->value,
            'save_count' => 0,
            'lend_count' => 0,
            'published_at' => now(),
        ];
    }
}