<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karma_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('delta');
            $table->string('reason', 64);
            $table->string('related_type', 64)->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        if (Schema::hasColumn('chat_messages', 'is_helpful')) {
            return;
        }

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->boolean('is_helpful')->default(false)->after('body');
            $table->unsignedBigInteger('marked_helpful_by_user_id')->nullable()->after('is_helpful');
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn(['is_helpful', 'marked_helpful_by_user_id']);
        });

        Schema::dropIfExists('karma_events');
    }
};