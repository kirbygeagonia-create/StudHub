<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 24);
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('year_level')->nullable();
            $table->unsignedBigInteger('request_id')->nullable();
            $table->string('title', 191);
            $table->string('slug', 191);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'slug']);
            $table->index(['kind', 'program_id', 'year_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};
