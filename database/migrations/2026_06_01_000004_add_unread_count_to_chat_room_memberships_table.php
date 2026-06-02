<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_room_memberships', function (Blueprint $table) {
            $table->unsignedInteger('unread_count')->default(0)->after('is_muted');
        });
    }

    public function down(): void
    {
        Schema::table('chat_room_memberships', function (Blueprint $table) {
            $table->dropColumn('unread_count');
        });
    }
};
