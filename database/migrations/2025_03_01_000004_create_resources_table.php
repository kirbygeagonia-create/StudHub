<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 32);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('course_code', 32)->nullable();
            $table->unsignedSmallInteger('year_taken')->nullable();
            $table->unsignedTinyInteger('year_level')->nullable();
            $table->string('condition', 16)->nullable();
            $table->string('availability', 24)->default('available');
            $table->string('visibility', 24)->default('school');
            $table->string('file_url', 500)->nullable();
            $table->string('file_mime', 100)->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->boolean('is_watermarked')->default(false);
            $table->unsignedInteger('save_count')->default(0);
            $table->unsignedInteger('lend_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['school_id', 'subject_id', 'availability']);
            $table->index(['school_id', 'type', 'subject_id']);
            $table->index(['school_id', 'program_id']);
            $table->index('owner_user_id');
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE resources ADD FULLTEXT resources_title_description_ft (title, description)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
