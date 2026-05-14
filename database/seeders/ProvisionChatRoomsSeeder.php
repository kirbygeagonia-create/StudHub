<?php

namespace Database\Seeders;

use App\Domain\Chat\Actions\EnsureProgramChatRooms;
use Illuminate\Database\Seeder;

class ProvisionChatRoomsSeeder extends Seeder
{
    public function __construct(private readonly EnsureProgramChatRooms $action = new EnsureProgramChatRooms) {}

    public function run(): void
    {
        $this->action->run();
    }
}
