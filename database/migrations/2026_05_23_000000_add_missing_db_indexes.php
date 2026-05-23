<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->change()->constrained('schools')->nullOnDelete();
            $table->index('school_id');
        });

        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->foreignId('request_id')->nullable()->change()->constrained('requests')->nullOnDelete();
            $table->index('request_id');
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->index('requester_user_id');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->index('sender_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('college_id');
            $table->index('role');
        });

        Schema::table('lends', function (Blueprint $table) {
            $table->index('escalated_at');
            $table->index('offer_id');
            $table->index('request_id');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->index('offerer_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropForeign(['school_id']);
        });

        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->dropIndex(['request_id']);
            $table->dropForeign(['request_id']);
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->dropIndex(['requester_user_id']);
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex(['sender_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['college_id']);
            $table->dropIndex(['role']);
        });

        Schema::table('lends', function (Blueprint $table) {
            $table->dropIndex(['escalated_at']);
            $table->dropIndex(['offer_id']);
            $table->dropIndex(['request_id']);
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex(['offerer_user_id']);
        });
    }
};
