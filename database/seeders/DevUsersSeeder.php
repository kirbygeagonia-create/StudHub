<?php

namespace Database\Seeders;

use App\Domain\Identity\Enums\UserRole;
use App\Models\Program;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevUsersSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'SEAIT')->firstOrFail();

        $bsit = Program::where('school_id', $school->id)->where('code', 'BSIT')->firstOrFail();
        $bsce = Program::where('school_id', $school->id)->where('code', 'BSCE')->firstOrFail();
        $bsbaMm = Program::where('school_id', $school->id)->where('code', 'BSBA-MM')->firstOrFail();

        $password = Hash::make('password');

        User::updateOrCreate(
            ['email' => 'test@seait.edu.ph'],
            [
                'school_id' => $school->id,
                'program_id' => $bsit->id,
                'college_id' => $bsit->college_id,
                'name' => 'Test Student',
                'display_name' => 'Test',
                'email_verified_at' => now(),
                'password' => $password,
                'role' => UserRole::Student,
                'year_level' => 2,
                'onboarded_at' => now(),
                'karma' => 50,
            ],
        );

        User::updateOrCreate(
            ['email' => 'mod@seait.edu.ph'],
            [
                'school_id' => $school->id,
                'program_id' => $bsce->id,
                'college_id' => $bsce->college_id,
                'name' => 'Moderator User',
                'display_name' => 'Moderator',
                'email_verified_at' => now(),
                'password' => $password,
                'role' => UserRole::Moderator,
                'year_level' => 3,
                'onboarded_at' => now(),
                'karma' => 120,
            ],
        );

        User::updateOrCreate(
            ['email' => 'admin@seait.edu.ph'],
            [
                'school_id' => $school->id,
                'program_id' => $bsbaMm->id,
                'college_id' => $bsbaMm->college_id,
                'name' => 'Admin User',
                'display_name' => 'Admin',
                'email_verified_at' => now(),
                'password' => $password,
                'role' => UserRole::Admin,
                'year_level' => 4,
                'onboarded_at' => now(),
                'karma' => 200,
            ],
        );

        $this->command->info('Dev accounts created:');
        $this->command->info('  Student  → test@seait.edu.ph / password');
        $this->command->info('  Moderator → mod@seait.edu.ph / password');
        $this->command->info('  Admin    → admin@seait.edu.ph / password');
    }
}
