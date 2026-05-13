<?php

namespace Database\Factories;

use App\Domain\Identity\Enums\UserRole;
use App\Models\College;
use App\Models\Program;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'display_name' => null,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => UserRole::Student->value,
            'karma' => 0,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Attach a SEAIT school + college + program + onboarding marker so the
     * user can pass the `onboarded` middleware.
     */
    public function onboarded(): static
    {
        return $this->state(function (array $attributes) {
            $school = School::firstOrCreate(
                ['code' => 'SEAIT'],
                [
                    'name' => 'South East Asian Institute of Technology, Inc.',
                    'short_name' => 'SEAIT',
                    'timezone' => 'Asia/Manila',
                    'email_domains' => ['seait.edu.ph'],
                    'location' => 'Tupi, South Cotabato',
                ],
            );

            $college = College::firstOrCreate(
                ['school_id' => $school->id, 'code' => 'CICT'],
                ['name' => 'College of Information and Communication Technology'],
            );

            $program = Program::firstOrCreate(
                ['school_id' => $school->id, 'code' => 'BSIT'],
                [
                    'college_id' => $college->id,
                    'name' => 'Bachelor of Science in Information Technology',
                    'default_year_levels' => 4,
                    'is_active' => true,
                ],
            );

            return [
                'school_id' => $school->id,
                'college_id' => $college->id,
                'program_id' => $program->id,
                'year_level' => 2,
                'display_name' => $attributes['name'] ?? fake()->firstName(),
                'onboarded_at' => now(),
            ];
        });
    }
}
