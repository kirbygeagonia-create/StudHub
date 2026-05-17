<?php

use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Requests\Actions\CreateRequest;
use App\Domain\Requests\Enums\RequestUrgency;
use App\Models\Request;
use App\Models\Subject;
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

it('creates a request and stamps it with the requester', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = (new CreateRequest)->handle($user, [
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
        'urgency' => RequestUrgency::Normal->value,
        'description' => 'Need a DSA reviewer for midterms',
    ]);

    expect($request)->toBeInstanceOf(Request::class);
    expect($request->requester_user_id)->toBe($user->id);
    expect($request->subject_id)->toBe($subject->id);
    expect($request->type_wanted)->toBe('reviewer');
    expect($request->status->value)->toBe('open');
    expect($request->urgency->value)->toBe('normal');
});

it('refuses to create a request for a subject in another school', function () {
    $user = User::factory()->onboarded()->create();
    $otherSchool = \App\Models\School::create([
        'code' => 'OTHER',
        'name' => 'Other School',
        'short_name' => 'OTHER',
        'timezone' => 'Asia/Manila',
        'email_domains' => ['other.edu.ph'],
    ]);
    $foreignSubject = Subject::create([
        'school_id' => $otherSchool->id,
        'code' => 'FOR 100',
        'name' => 'Foreign subject',
        'domain' => 'Other',
        'is_active' => true,
    ]);

    expect(fn () => (new CreateRequest)->handle($user, [
        'subject_id' => $foreignSubject->id,
        'type_wanted' => ResourceType::Reviewer->value,
    ]))->toThrow(RuntimeException::class);
});

it('refuses to create a request when user already has 5 open requests', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    for ($i = 0; $i < 5; $i++) {
        Request::factory()->create([
            'requester_user_id' => $user->id,
            'subject_id' => $subject->id,
            'status' => 'open',
        ]);
    }

    expect(fn () => (new CreateRequest)->handle($user, [
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
    ]))->toThrow(RuntimeException::class);
});

it('requires an onboarded user with a school', function () {
    $user = User::factory()->create();

    expect(fn () => (new CreateRequest)->handle($user, [
        'subject_id' => 1,
        'type_wanted' => ResourceType::Reviewer->value,
    ]))->toThrow(RuntimeException::class);
});

it('refuses to create a request if posted within 10 minutes of the last one', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    (new CreateRequest)->handle($user, [
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
        'urgency' => RequestUrgency::Normal->value,
    ]);

    expect(fn () => (new CreateRequest)->handle($user, [
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
        'urgency' => RequestUrgency::Normal->value,
    ]))->toThrow(RuntimeException::class);
});

it('stores a request via the POST route', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $this->actingAs($user)
        ->post(route('requests.store'), [
            'subject_id' => $subject->id,
            'type_wanted' => ResourceType::Reviewer->value,
            'urgency' => RequestUrgency::Normal->value,
            'description' => 'Need help with DSA',
        ])->assertRedirect();

    expect(Request::where('requester_user_id', $user->id)->count())->toBe(1);
});

it('renders the request create page', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->get(route('requests.create'))
        ->assertOk()
        ->assertSee('New Request');
});

it('renders the request board index page', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->get(route('requests.index'))
        ->assertOk()
        ->assertSee('Request Board');
});