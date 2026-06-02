<?php

use App\Domain\Identity\Enums\UserRole;
use App\Models\Feedback;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;
use Database\Seeders\SeaitSubjectsSeeder;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);
});

it('renders the sao dashboard for an sao user', function () {
    $sao = User::factory()->onboarded()->create([
        'role' => UserRole::Sao,
    ]);

    $this->actingAs($sao)
        ->get(route('sao.dashboard'))
        ->assertOk();
});

it('blocks students from the sao dashboard', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->get(route('sao.dashboard'))
        ->assertForbidden();
});

it('renders the sao feedback page', function () {
    $sao = User::factory()->onboarded()->create([
        'role' => UserRole::Sao,
    ]);

    $this->actingAs($sao)
        ->get(route('sao.feedback'))
        ->assertOk();
});

it('shows feedback routed to sao and super_admin', function () {
    $sao = User::factory()->onboarded()->create([
        'role' => UserRole::Sao,
    ]);
    $student = User::factory()->onboarded()->create();

    Feedback::create([
        'user_id' => $student->id,
        'type' => 'general',
        'body' => 'SAO feedback test',
        'recipient_role' => 'sao',
        'status' => 'open',
    ]);

    Feedback::create([
        'user_id' => $student->id,
        'type' => 'general',
        'body' => 'Super admin feedback test',
        'recipient_role' => 'super_admin',
        'status' => 'open',
    ]);

    $this->actingAs($sao)
        ->get(route('sao.feedback'))
        ->assertSee('SAO feedback test')
        ->assertSee('Super admin feedback test');
});

it('renders the sao users page', function () {
    $sao = User::factory()->onboarded()->create([
        'role' => UserRole::Sao,
    ]);

    $this->actingAs($sao)
        ->get(route('sao.users'))
        ->assertOk();
});
