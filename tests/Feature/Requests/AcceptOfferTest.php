<?php

use App\Domain\Requests\Actions\AcceptOffer;
use App\Domain\Requests\Enums\OfferStatus;
use App\Domain\Requests\Enums\RequestStatus;
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

it('accepts an offer and marks the request as matched', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
    ]);

    $offer = Offer::create([
        'request_id' => $request->id,
        'offerer_user_id' => $offerer->id,
        'message' => 'I can help!',
        'status' => 'pending',
    ]);

    (new AcceptOffer)->handle($requester, $request, $offer);

    expect($offer->refresh()->status)->toBe(OfferStatus::Accepted);
    expect($request->refresh()->status)->toBe(RequestStatus::Matched);
    expect($request->refresh()->fulfilled_offer_id)->toBe($offer->id);
});

it('rejects other pending offers when one is accepted', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer1 = User::factory()->onboarded()->create();
    $offerer2 = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
    ]);

    $offer1 = Offer::create([
        'request_id' => $request->id,
        'offerer_user_id' => $offerer1->id,
        'status' => 'pending',
    ]);
    $offer2 = Offer::create([
        'request_id' => $request->id,
        'offerer_user_id' => $offerer2->id,
        'status' => 'pending',
    ]);

    (new AcceptOffer)->handle($requester, $request, $offer1);

    expect($offer1->refresh()->status)->toBe(OfferStatus::Accepted);
    expect($offer2->refresh()->status)->toBe(OfferStatus::Rejected);
});

it('refuses if a non-requester tries to accept', function () {
    $requester = User::factory()->onboarded()->create();
    $otherUser = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
    ]);
    $offer = Offer::create([
        'request_id' => $request->id,
        'offerer_user_id' => $offerer->id,
        'status' => 'pending',
    ]);

    expect(fn () => (new AcceptOffer)->handle($otherUser, $request, $offer))
        ->toThrow(RuntimeException::class);
});

it('refuses to accept an already-accepted offer', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
    ]);
    $offer = Offer::create([
        'request_id' => $request->id,
        'offerer_user_id' => $offerer->id,
        'status' => 'accepted',
    ]);

    expect(fn () => (new AcceptOffer)->handle($requester, $request, $offer))
        ->toThrow(RuntimeException::class);
});

it('accepts an offer via the accept route', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
    ]);
    $offer = Offer::create([
        'request_id' => $request->id,
        'offerer_user_id' => $offerer->id,
        'status' => 'pending',
    ]);

    $this->actingAs($requester)
        ->post(route('requests.offers.accept', [$request, $offer]))
        ->assertRedirect();

    expect($request->refresh()->status)->toBe(RequestStatus::Matched);
});