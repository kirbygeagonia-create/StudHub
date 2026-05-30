<?php

namespace Database\Seeders;

use App\Domain\Identity\Enums\UserRole;
use App\Models\College;
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
        $cict = College::where('school_id', $school->id)->where('code', 'CICT')->firstOrFail();

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

        $cbgg = College::where('code', 'CBGG')->where('school_id', $school->id)->firstOrFail();

        User::updateOrCreate(
            ['email' => 'admin@seait.edu.ph'],
            [
                'school_id' => $school->id,
                'college_id' => $cbgg->id,
                'program_id' => null,
                'year_level' => null,
                'name' => 'Program Head CBGG',
                'display_name' => 'Program Head',
                'email_verified_at' => now(),
                'password' => $password,
                'role' => UserRole::ProgramHead,
                'onboarded_at' => now(),
                'karma' => 200,
            ],
        );

        // Dean dev account (CICT college)
        User::updateOrCreate(
            ['email' => 'dean.cict@seait.edu.ph'],
            [
                'school_id' => $school->id,
                'college_id' => $cict->id,
                'name' => 'Dean CICT',
                'display_name' => 'Dean',
                'email_verified_at' => now(),
                'password' => $password,
                'role' => UserRole::Dean,
                'onboarded_at' => now(),
            ],
        );

        // SAO dev account (campus-wide authority)
        User::updateOrCreate(
            ['email' => 'sao@seait.edu.ph'],
            [
                'school_id' => $school->id,
                'name' => 'SAO Officer',
                'display_name' => 'SAO',
                'email_verified_at' => now(),
                'password' => $password,
                'role' => UserRole::Sao,
                'onboarded_at' => now(),
            ],
        );

        $this->command->info('Dev accounts created:');
        $this->command->info('  Student      → test@seait.edu.ph / password');
        $this->command->info('  Moderator    → mod@seait.edu.ph / password');
        $this->command->info('  Program Head → admin@seait.edu.ph / password');
        $this->command->info('  Dean         → dean.cict@seait.edu.ph / password');
        $this->command->info('  SAO          → sao@seait.edu.ph / password');
    }
}
