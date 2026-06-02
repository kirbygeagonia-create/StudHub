<?php

use App\Models\Subject;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;
use Database\Seeders\SeaitSubjectsSeeder;

it('returns a successful response from the home page', function () {
    $response = $this->get('/');

    expect($response->status())->toBe(200);
});

it('exposes the configured app name', function () {
    expect(config('app.name'))->toBeString()->not->toBeEmpty();
});

it('uses Asia/Manila as the default timezone for the SEAIT pilot', function () {
    expect(config('app.timezone'))->toBe('Asia/Manila');
});

it('redirects /help to the welcome page with modal trigger', function () {
    $this->get('/help')
        ->assertRedirect('/?open=help');
});

it('redirects /aup to the welcome page with modal trigger', function () {
    $this->get('/aup')
        ->assertRedirect('/?open=aup');
});

it('serves the landing page with StudHub branding', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('StudHub')
        ->assertSee('SEAIT');
});

it('redirects unauthenticated users from dashboard to login', function () {
    $this->get('/dashboard')
        ->assertRedirect('/login');
});

it('rate limits excessive POST requests', function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);

    $user = User::factory()->onboarded()->create();
    $subject = Subject::where('code', 'IT 211')->first();

    if ($subject === null) {
        $this->markTestSkipped('No subject seeded.');
    }

    $i = 0;
    while ($i < 11) {
        $response = $this->actingAs($user)
            ->post('/requests', [
                'subject_id' => $subject->id,
                'type_wanted' => 'reviewer',
                'urgency' => 'normal',
            ]);
        $i++;
    }

    // After exceeding the throttle limit (10 per minute), the next request
    // should be throttled. Laravel's throttle middleware returns a redirect
    // in test environment rather than a 429 status.
    $response->assertStatus(429);
})->group('throttle');

it('returns 200 from the up healthcheck endpoint', function () {
    $this->get('/up')->assertOk();
});

it('renders the login page', function () {
    $this->get('/login')->assertOk();
});

it('renders the registration page', function () {
    $this->get('/register')->assertOk();
});

it('authenticated dashboard loads for onboarded user', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk();
});

it('leaderboard page loads', function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);

    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->get('/leaderboard')
        ->assertOk();
});

it('catalog browse loads for authenticated user', function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);

    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->get('/resources')
        ->assertOk();
});

it('request board loads for authenticated user', function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);

    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->get('/requests')
        ->assertOk();
});

it('expire requests command runs without error', function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);

    $this->artisan('studhub:expire-requests')
        ->assertExitCode(0);
});
