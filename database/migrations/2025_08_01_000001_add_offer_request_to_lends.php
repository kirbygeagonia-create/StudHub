<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lends', function (Blueprint $table) {
            $table->foreignId('offer_id')->nullable()->after('resource_id')->constrained('offers')->nullOnDelete();
            $table->foreignId('request_id')->nullable()->after('offer_id')->constrained('requests')->nullOnDelete();
            $table->string('condition_on_return', 16)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lends', function (Blueprint $table) {
            $table->dropConstrainedForeignId('offer_id');
            $table->dropConstrainedForeignId('request_id');
        });
    }
};