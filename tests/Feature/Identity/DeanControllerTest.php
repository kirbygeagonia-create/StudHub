<?php

use App\Domain\Identity\Enums\UserRole;
use App\Models\College;
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

it('renders the dean dashboard for a dean user', function () {
    $college = College::first();
    $dean = User::factory()->onboarded()->create([
        'role' => UserRole::Dean,
        'college_id' => $college->id,
    ]);

    $this->actingAs($dean)
        ->get(route('dean.dashboard'))
        ->assertOk();
});

it('blocks students from the dean dashboard', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->get(route('dean.dashboard'))
        ->assertForbidden();
});

it('renders the dean feedback page', function () {
    $college = College::first();
    $dean = User::factory()->onboarded()->create([
        'role' => UserRole::Dean,
        'college_id' => $college->id,
    ]);

    $this->actingAs($dean)
        ->get(route('dean.feedback'))
        ->assertOk();
});

it('shows feedback routed to the dean', function () {
    $college = College::first();
    $dean = User::factory()->onboarded()->create([
        'role' => UserRole::Dean,
        'college_id' => $college->id,
    ]);
    $student = User::factory()->onboarded()->create();

    Feedback::create([
        'user_id' => $student->id,
        'type' => 'general',
        'body' => 'Test feedback for dean',
        'recipient_role' => 'dean',
        'recipient_college_id' => $college->id,
        'status' => 'open',
    ]);

    $this->actingAs($dean)
        ->get(route('dean.feedback'))
        ->assertSee('Test feedback for dean');
});

it('renders the dean programs page', function () {
    $college = College::first();
    $dean = User::factory()->onboarded()->create([
        'role' => UserRole::Dean,
        'college_id' => $college->id,
    ]);

    $this->actingAs($dean)
        ->get(route('dean.programs'))
        ->assertOk();
});
