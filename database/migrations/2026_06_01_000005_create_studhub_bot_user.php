<?php

use App\Domain\Identity\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create or update the StudHub Bot system user
        DB::table('users')->updateOrInsert(
            ['email' => 'bot@studhub.local'],
            [
                'name' => 'StudHub Bot',
                'display_name' => 'StudHub Bot',
                'password' => bcrypt('nologin-' . bin2hex(random_bytes(16))),
                'role' => UserRole::System->value,
                'email_verified_at' => now(),
                'onboarded_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'bot@studhub.local')->delete();
    }
};
