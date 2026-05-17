<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120)->default('My Shelf');
            $table->timestamps();

            $table->unique(['user_id', 'name']);
        });

        Schema::create('shelf_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelf_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->unique(['shelf_id', 'resource_id']);
            $table->index(['resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shelf_items');
        Schema::dropIfExists('shelves');
    }
};