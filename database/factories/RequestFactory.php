<?php

namespace Database\Factories;

use App\Domain\Requests\Enums\RequestStatus;
use App\Domain\Requests\Enums\RequestUrgency;
use App\Models\ResourceRequest;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResourceRequest>
 */
class RequestFactory extends Factory
{
    protected $model = ResourceRequest::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requester_user_id' => User::factory(),
            'subject_id' => Subject::factory(),
            'type_wanted' => 'reviewer',
            'urgency' => RequestUrgency::Normal->value,
            'needed_by' => null,
            'description' => fake()->optional()->sentence(),
            'status' => RequestStatus::Open->value,
        ];
    }
}
