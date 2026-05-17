<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('type_wanted', 32);
            $table->string('urgency', 16)->default('normal');
            $table->date('needed_by')->nullable();
            $table->text('description')->nullable();
            $table->string('status', 16)->default('open');
            $table->foreignId('fulfilled_offer_id')->nullable();
            $table->timestamps();

            $table->index(['status', 'subject_id']);
        });

        Schema::create('request_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 3);
            $table->integer('notified_user_count')->default(0);
            $table->timestamps();
        });

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->cascadeOnDelete();
            $table->foreignId('offerer_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('resource_id')->nullable()->constrained('resources')->nullOnDelete();
            $table->text('message')->nullable();
            $table->string('status', 16)->default('pending');
            $table->timestamps();

            $table->unique(['request_id', 'offerer_user_id']);
        });

        Schema::create('lends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('to_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('lent_at')->useCurrent();
            $table->date('return_by')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->string('condition_on_return', 16)->nullable();
            $table->timestamps();
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->foreign('fulfilled_offer_id')->references('id')->on('offers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['fulfilled_offer_id']);
        });

        Schema::dropIfExists('lends');
        Schema::dropIfExists('offers');
        Schema::dropIfExists('request_routes');
        Schema::dropIfExists('requests');
    }
};