<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table): void {
            // Drop the existing foreign key and index, re-add with nullOnDelete
            $table->dropForeign(['owner_user_id']);
            $table->foreignId('owner_user_id')->nullable()->change();
            $table->foreign('owner_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table): void {
            $table->dropForeign(['owner_user_id']);
            $table->foreignId('owner_user_id')->nullable(false)->change();
            $table->foreign('owner_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
