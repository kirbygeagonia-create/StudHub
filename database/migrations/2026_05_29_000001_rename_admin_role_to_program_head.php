<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite does not support ALTER TABLE MODIFY COLUMN or ENUM types.
        // The role column is stored as a plain string in SQLite, so the
        // enum-altering statements only run on MySQL.
        if (DB::getDriverName() === 'mysql') {
            // Step 1: Extend enum to include new values + keep old ones temporarily
            DB::statement("ALTER TABLE users MODIFY COLUMN role
                ENUM('student','moderator','admin','program_head','dean','sao','super_admin')
                NOT NULL DEFAULT 'student'");
        }

        // Step 2: Migrate existing data (works on both MySQL and SQLite)
        DB::statement("UPDATE users SET role = 'program_head' WHERE role = 'admin'");

        if (DB::getDriverName() === 'mysql') {
            // Step 3: Remove old 'admin' from enum
            DB::statement("ALTER TABLE users MODIFY COLUMN role
                ENUM('student','moderator','program_head','dean','sao','super_admin')
                NOT NULL DEFAULT 'student'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role
                ENUM('student','moderator','admin','program_head','dean','sao','super_admin')
                NOT NULL DEFAULT 'student'");
        }

        DB::statement("UPDATE users SET role = 'admin' WHERE role = 'program_head'");

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role
                ENUM('student','moderator','admin','super_admin')
                NOT NULL DEFAULT 'student'");
        }
    }
};
