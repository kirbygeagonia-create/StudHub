<?php

namespace Database\Factories;

use App\Domain\Requests\Enums\OfferStatus;
use App\Models\LearningResource;
use App\Models\Offer;
use App\Models\ResourceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offer>
 */
class OfferFactory extends Factory
{
    protected $model = Offer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'request_id' => ResourceRequest::factory(),
            'offerer_user_id' => User::factory(),
            'resource_id' => LearningResource::factory(),
            'message' => fake()->optional()->sentence(),
            'status' => OfferStatus::Pending->value,
        ];
    }
}
