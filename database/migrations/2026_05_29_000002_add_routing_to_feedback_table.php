<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            // Which role-level should receive/see this feedback
            $table->string('recipient_role', 20)->default('sao')->after('type');
            // College/program scope for routing
            $table->foreignId('recipient_college_id')->nullable()->constrained('colleges')->nullOnDelete();
            $table->foreignId('recipient_program_id')->nullable()->constrained('programs')->nullOnDelete();
            // Escalation chain
            $table->foreignId('escalated_from_id')->nullable()->constrained('feedback')->nullOnDelete();
            // Status
            $table->string('status', 16)->default('open');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_note')->nullable();

            $table->index(['recipient_role', 'status']);
            $table->index(['recipient_program_id', 'status']);
            $table->index(['recipient_college_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropIndex(['recipient_role', 'status']);
            $table->dropIndex(['recipient_program_id', 'status']);
            $table->dropIndex(['recipient_college_id', 'status']);

            $table->dropColumn([
                'recipient_role',
                'recipient_college_id',
                'recipient_program_id',
                'escalated_from_id',
                'status',
                'read_at',
                'resolved_at',
                'resolution_note',
            ]);
        });
    }
};
