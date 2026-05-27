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
    expect(BadgeTier::fromKarma(0))->toBe(BadgeTier::Seedling);
    expect(BadgeTier::fromKarma(24))->toBe(BadgeTier::Seedling);
    expect(BadgeTier::fromKarma(25))->toBe(BadgeTier::Bookworm);
    expect(BadgeTier::fromKarma(74))->toBe(BadgeTier::Bookworm);
    expect(BadgeTier::fromKarma(75))->toBe(BadgeTier::Scribe);
    expect(BadgeTier::fromKarma(149))->toBe(BadgeTier::Scribe);
    expect(BadgeTier::fromKarma(150))->toBe(BadgeTier::Scholar);
    expect(BadgeTier::fromKarma(500))->toBe(BadgeTier::Pathfinder);
    expect(BadgeTier::fromKarma(6000))->toBe(BadgeTier::StudHubLegend);
});

it('renders the profile page with karma and badge', function () {
    $user = User::factory()->onboarded()->create(['karma' => 80]);

    $this->actingAs($user)
        ->get(route('profile.show'))
        ->assertOk()
        ->assertSee('Karma')
        ->assertSee('80')
        ->assertSee('Scribe');
});

it('renders the leaderboard for the user\'s program', function () {
    $user = User::factory()->onboarded()->create(['karma' => 50]);

    User::factory()->onboarded()->create(['karma' => 100]);
    User::factory()->onboarded()->create(['karma' => 30]);

    $this->actingAs($user)
        ->get(route('leaderboard'))
        ->assertOk()
        ->assertSee('Leaderboard');
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

it('KarmaEventReason returns correct points for each reason', function () {
    expect(KarmaEventReason::ResourceUploaded->delta())->toBe(5);
    expect(KarmaEventReason::ResourceSaved->delta())->toBe(5);
    expect(KarmaEventReason::RequestFulfilled->delta())->toBe(10);
    expect(KarmaEventReason::ChatMarkedHelpful->delta())->toBe(2);
    expect(KarmaEventReason::ReportConfirmed->delta())->toBe(-5);
});

it('KarmaEventReason values returns all cases', function () {
    $values = KarmaEventReason::values();
    expect($values)->toContain('resource_uploaded');
    expect($values)->toContain('resource_saved');
    expect($values)->toContain('request_fulfilled');
    expect($values)->toContain('report_confirmed');
});

it('BadgeTier labels are correct', function () {
    expect(BadgeTier::Seedling->label())->toBe('Seedling');
    expect(BadgeTier::Bookworm->label())->toBe('Bookworm');
    expect(BadgeTier::Scribe->label())->toBe('Scribe');
    expect(BadgeTier::Scholar->label())->toBe('Scholar');
    expect(BadgeTier::Illuminator->label())->toBe('Illuminator');
    expect(BadgeTier::Pathfinder->label())->toBe('Pathfinder');
    expect(BadgeTier::Sage->label())->toBe('Sage');
    expect(BadgeTier::Luminary->label())->toBe('Luminary');
    expect(BadgeTier::Archivist->label())->toBe('Archivist');
    expect(BadgeTier::Oracle->label())->toBe('Oracle');
    expect(BadgeTier::Custodian->label())->toBe('Custodian');
    expect(BadgeTier::StudHubLegend->label())->toBe('StudHub Legend');
});

it('BadgeTier threshold returns correct karma requirements', function () {
    expect(BadgeTier::Seedling->threshold())->toBe(0);
    expect(BadgeTier::Bookworm->threshold())->toBe(25);
    expect(BadgeTier::Scribe->threshold())->toBe(75);
    expect(BadgeTier::Scholar->threshold())->toBe(150);
    expect(BadgeTier::Illuminator->threshold())->toBe(300);
    expect(BadgeTier::Pathfinder->threshold())->toBe(500);
    expect(BadgeTier::Sage->threshold())->toBe(750);
    expect(BadgeTier::Luminary->threshold())->toBe(1000);
    expect(BadgeTier::Archivist->threshold())->toBe(1500);
    expect(BadgeTier::Oracle->threshold())->toBe(2500);
    expect(BadgeTier::Custodian->threshold())->toBe(4000);
    expect(BadgeTier::StudHubLegend->threshold())->toBe(6000);
});

it('BadgeTier fromKarma correctly resolves tiers', function () {
    expect(BadgeTier::fromKarma(0))->toBe(BadgeTier::Seedling);
    expect(BadgeTier::fromKarma(25))->toBe(BadgeTier::Bookworm);
    expect(BadgeTier::fromKarma(74))->toBe(BadgeTier::Bookworm);
    expect(BadgeTier::fromKarma(75))->toBe(BadgeTier::Scribe);
    expect(BadgeTier::fromKarma(149))->toBe(BadgeTier::Scribe);
    expect(BadgeTier::fromKarma(150))->toBe(BadgeTier::Scholar);
    expect(BadgeTier::fromKarma(500))->toBe(BadgeTier::Pathfinder);
    expect(BadgeTier::fromKarma(6000))->toBe(BadgeTier::StudHubLegend);
});
