<?php

namespace Database\Factories;

use App\Domain\Chat\Enums\ChatRoomKind;
use App\Models\ChatRoom;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatRoom>
 */
class ChatRoomFactory extends Factory
{
    protected $model = ChatRoom::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'kind' => ChatRoomKind::Program->value,
            'title' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
        ];
    }
}
