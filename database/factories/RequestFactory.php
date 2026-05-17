<?php

namespace Database\Factories;

use App\Domain\Requests\Enums\RequestStatus;
use App\Domain\Requests\Enums\RequestUrgency;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Request>
 */
class RequestFactory extends Factory
{
    protected $model = Request::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requester_user_id' => User::factory(),
            'subject_id' => null,
            'type_wanted' => 'reviewer',
            'urgency' => RequestUrgency::Normal->value,
            'needed_by' => null,
            'description' => fake()->optional()->sentence(),
            'status' => RequestStatus::Open->value,
        ];
    }
}