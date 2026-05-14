<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_subjects', function (Blueprint $table) {
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('typical_year_level')->nullable();
            $table->decimal('weight', 4, 3)->default(1.000);
            $table->timestamps();

            $table->primary(['program_id', 'subject_id']);
            $table->index(['subject_id', 'weight']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_subjects');
    }
};
