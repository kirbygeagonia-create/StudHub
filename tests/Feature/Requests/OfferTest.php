<?php

use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Requests\Actions\CreateOffer;
use App\Domain\Requests\Enums\OfferStatus;
use App\Models\LearningResource;
use App\Models\Offer;
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

it('creates an offer on an open request', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
    ]);

    $offer = (new CreateOffer)->handle($offerer, $request, [
        'message' => 'I have a great reviewer!',
    ]);

    expect($offer)->toBeInstanceOf(Offer::class);
    expect($offer->request_id)->toBe($request->id);
    expect($offer->offerer_user_id)->toBe($offerer->id);
    expect($offer->status)->toBe(OfferStatus::Pending);
    expect($offer->message)->toBe('I have a great reviewer!');
});

it('refuses an offer from the requester themselves', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $user->id,
        'subject_id' => $subject->id,
    ]);

    expect(fn () => (new CreateOffer)->handle($user, $request, []))
        ->toThrow(RuntimeException::class);
});

it('refuses a second offer from the same user', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
    ]);

    (new CreateOffer)->handle($offerer, $request, ['message' => 'First offer']);

    expect(fn () => (new CreateOffer)->handle($offerer, $request, ['message' => 'Second offer']))
        ->toThrow(RuntimeException::class);
});

it('refuses an offer on a closed request', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
        'status' => 'fulfilled',
    ]);

    expect(fn () => (new CreateOffer)->handle($offerer, $request, []))
        ->toThrow(RuntimeException::class);
});

it('accepts an offer with a matching resource', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $resource = LearningResource::factory()->create([
        'school_id' => $offerer->school_id,
        'owner_user_id' => $offerer->id,
        'subject_id' => $subject->id,
        'program_id' => $offerer->program_id,
        'type' => ResourceType::Reviewer->value,
    ]);

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
    ]);

    $offer = (new CreateOffer)->handle($offerer, $request, [
        'resource_id' => $resource->id,
        'message' => 'Here is my reviewer!',
    ]);

    expect($offer->resource_id)->toBe($resource->id);
});

it('posts an offer via the route', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
    ]);

    $this->actingAs($offerer)
        ->post(route('requests.offers.store', $request), [
            'message' => 'I can help!',
        ])->assertRedirect();

    expect(Offer::where('request_id', $request->id)->count())->toBe(1);
});