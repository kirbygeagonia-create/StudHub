<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Stored as the Badge enum string value (e.g. 'page_turner').
            // Using varchar(64) — no enum column type — so new Badge cases
            // can be added without a schema migration.
            $table->string('badge', 64);

            $table->timestamp('earned_at')->useCurrent();

            // Each badge is earned at most once per user.
            $table->unique(['user_id', 'badge']);

            $table->index(['user_id', 'earned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};