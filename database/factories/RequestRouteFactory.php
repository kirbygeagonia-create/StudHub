<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\Request;
use App\Models\RequestRoute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestRoute>
 */
class RequestRouteFactory extends Factory
{
    protected $model = RequestRoute::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'request_id' => Request::factory(),
            'program_id' => Program::factory(),
            'score' => 1.0,
            'notified_user_count' => 0,
        ];
    }
}
