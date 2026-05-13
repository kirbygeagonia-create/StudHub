<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('college_id')->nullable()->after('school_id')->constrained()->nullOnDelete();
            $table->foreignId('program_id')->nullable()->after('college_id')->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('year_level')->nullable()->after('program_id');
            $table->string('display_name', 120)->nullable()->after('name');
            $table->string('avatar_url', 500)->nullable()->after('display_name');
            $table->string('role', 16)->default('student')->after('password');
            $table->unsignedInteger('karma')->default(0)->after('role');
            $table->timestamp('last_seen_at')->nullable()->after('karma');
            $table->timestamp('onboarded_at')->nullable()->after('last_seen_at');

            $table->index(['school_id', 'program_id', 'year_level']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['school_id', 'program_id', 'year_level']);
            $table->dropConstrainedForeignId('school_id');
            $table->dropConstrainedForeignId('college_id');
            $table->dropConstrainedForeignId('program_id');
            $table->dropColumn([
                'year_level',
                'display_name',
                'avatar_url',
                'role',
                'karma',
                'last_seen_at',
                'onboarded_at',
            ]);
        });
    }
};
