<?php

use App\Domain\Identity\Enums\UserRole;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
});

it('User::inheritedRoles returns correct roles for SuperAdmin', function () {
    $user = User::factory()->onboarded()->create(['role' => UserRole::SuperAdmin]);
    expect($user->inheritedRoles())->toContain('super_admin', 'sao', 'dean', 'program_head', 'moderator');
});

it('User::inheritedRoles returns correct roles for Dean', function () {
    $user = User::factory()->onboarded()->create(['role' => UserRole::Dean]);
    expect($user->inheritedRoles())->toContain('dean', 'program_head', 'moderator');
    expect($user->inheritedRoles())->not->toContain('super_admin', 'sao');
});

it('User::inheritedRoles returns correct roles for Student', function () {
    $user = User::factory()->onboarded()->create(['role' => UserRole::Student]);
    expect($user->inheritedRoles())->toBe(['student']);
});

it('EnsureHasRole middleware allows inherited role access', function () {
    $dean = User::factory()->onboarded()->create(['role' => UserRole::Dean]);

    $this->actingAs($dean)
        ->get(route('dashboard'))
        ->assertOk();
});
