<?php

use App\Domain\Reputation\Actions\AwardKarma;
use App\Domain\Reputation\Enums\BadgeTier;
use App\Domain\Reputation\Enums\KarmaEventReason;
use App\Models\KarmaEvent;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
});

it('creates a karma event and recalculates user karma', function () {
    $user = User::factory()->onboarded()->create();

    (new AwardKarma)->handle($user, KarmaEventReason::ResourceUploaded);

    expect(KarmaEvent::where('user_id', $user->id)->count())->toBe(1);
    expect($user->refresh()->karma)->toBe(5);

    $event = KarmaEvent::where('user_id', $user->id)->first();
    expect($event->delta)->toBe(5);
    expect($event->reason)->toBe('resource_uploaded');
});

it('awards +5 karma for uploading a resource', function () {
    $user = User::factory()->onboarded()->create();

    (new AwardKarma)->handle($user, KarmaEventReason::ResourceUploaded);

    expect($user->refresh()->karma)->toBe(5);
});

it('awards +5 karma when a resource gets saved', function () {
    $user = User::factory()->onboarded()->create();

    (new AwardKarma)->handle($user, KarmaEventReason::ResourceSaved);

    expect($user->refresh()->karma)->toBe(5);
});

it('awards +10 karma for fulfilling a request', function () {
    $user = User::factory()->onboarded()->create();

    (new AwardKarma)->handle($user, KarmaEventReason::RequestFulfilled);

    expect($user->refresh()->karma)->toBe(10);
});

it('awards +2 karma for a chat message marked helpful', function () {
    $user = User::factory()->onboarded()->create();

    (new AwardKarma)->handle($user, KarmaEventReason::ChatMarkedHelpful);

    expect($user->refresh()->karma)->toBe(2);
});

it('accumulates karma across multiple events', function () {
    $user = User::factory()->onboarded()->create();

    (new AwardKarma)->handle($user, KarmaEventReason::ResourceUploaded);
    (new AwardKarma)->handle($user, KarmaEventReason::ResourceSaved);
    (new AwardKarma)->handle($user, KarmaEventReason::ResourceSaved);

    expect($user->refresh()->karma)->toBe(15);
});

it('awards -5 karma for a confirmed report', function () {
    $user = User::factory()->onboarded()->create();
    $user->karma = 10;
    $user->save();

    (new AwardKarma)->handle($user, KarmaEventReason::ReportConfirmed);

    expect(KarmaEvent::where('user_id', $user->id)->sum('delta'))->toBe(-5);
});

it('assigns the correct badge tier based on karma', function () {
    expect(BadgeTier::fromKarmaOrNull(0))->toBeNull();
    expect(BadgeTier::fromKarmaOrNull(24))->toBeNull();
    expect(BadgeTier::fromKarmaOrNull(25))->toBe(BadgeTier::Bronze);
    expect(BadgeTier::fromKarmaOrNull(74))->toBe(BadgeTier::Bronze);
    expect(BadgeTier::fromKarmaOrNull(75))->toBe(BadgeTier::Silver);
    expect(BadgeTier::fromKarmaOrNull(149))->toBe(BadgeTier::Silver);
    expect(BadgeTier::fromKarmaOrNull(150))->toBe(BadgeTier::Gold);
    expect(BadgeTier::fromKarmaOrNull(500))->toBe(BadgeTier::Gold);
});

it('renders the profile page with karma and badge', function () {
    $user = User::factory()->onboarded()->create(['karma' => 80]);

    $this->actingAs($user)
        ->get(route('profile.show'))
        ->assertOk()
        ->assertSee('Karma')
        ->assertSee('80')
        ->assertSee('Silver');
});

it('renders the leaderboard for the user\'s program', function () {
    $user = User::factory()->onboarded()->create(['karma' => 50]);

    User::factory()->onboarded()->create(['karma' => 100]);
    User::factory()->onboarded()->create(['karma' => 30]);

    $this->actingAs($user)
        ->get(route('leaderboard'))
        ->assertOk()
        ->assertSee('Top Sharers');
});

it('sorts leaderboard by karma descending', function () {
    $user = User::factory()->onboarded()->create(['karma' => 0, 'display_name' => 'Alice']);
    $highKarma = User::factory()->onboarded()->create(['karma' => 100, 'display_name' => 'Bob']);
    $lowKarma = User::factory()->onboarded()->create(['karma' => 20, 'display_name' => 'Carol']);

    $this->actingAs($user)
        ->get(route('leaderboard'))
        ->assertOk()
        ->assertSeeInOrder(['Bob', 'Carol']);
});