<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lends', function (Blueprint $table) {
            $table->unsignedInteger('reminder_count')->default(0)->after('condition_on_return');
            $table->timestamp('escalated_at')->nullable()->after('reminder_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lends', function (Blueprint $table) {
            $table->dropColumn(['escalated_at', 'reminder_count']);
        });
    }
};
