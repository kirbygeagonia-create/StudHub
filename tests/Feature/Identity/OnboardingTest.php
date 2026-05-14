<?php

use App\Models\Program;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
});

it('redirects authenticated users without onboarding from the dashboard to /onboarding', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('onboarding.show', absolute: false));
});

it('does not redirect already-onboarded users away from the dashboard', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk();
});

it('renders the onboarding form with active SEAIT programs', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/onboarding');

    $response->assertOk();
    $response->assertSee('BSIT');
    $response->assertSee('BSCE');
});

it('persists program, year, and display name on submit', function () {
    $user = User::factory()->create();
    $bsit = Program::where('code', 'BSIT')->firstOrFail();

    $response = $this->actingAs($user)->post('/onboarding', [
        'program_id' => $bsit->id,
        'year_level' => 3,
        'display_name' => 'Jane E.',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));

    $user->refresh();
    expect($user->program_id)->toBe($bsit->id);
    expect($user->college_id)->toBe($bsit->college_id);
    expect($user->school_id)->toBe($bsit->school_id);
    expect($user->year_level)->toBe(3);
    expect($user->display_name)->toBe('Jane E.');
    expect($user->onboarded_at)->not->toBeNull();
});

it('rejects invalid year levels', function () {
    $user = User::factory()->create();
    $bsit = Program::where('code', 'BSIT')->firstOrFail();

    $response = $this->actingAs($user)->from('/onboarding')->post('/onboarding', [
        'program_id' => $bsit->id,
        'year_level' => 99,
        'display_name' => 'Whoever',
    ]);

    $response->assertRedirect('/onboarding');
    $response->assertSessionHasErrors('year_level');

    $user->refresh();
    expect($user->onboarded_at)->toBeNull();
});

it('redirects already-onboarded users away from the onboarding screen', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->get('/onboarding')
        ->assertRedirect(route('dashboard', absolute: false));
});
