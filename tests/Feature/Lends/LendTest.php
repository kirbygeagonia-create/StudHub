<?php

use App\Domain\Catalog\Enums\ResourceAvailability;
use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Lends\Actions\RecordLend;
use App\Domain\Lends\Actions\ReturnResource;
use App\Domain\Lends\Enums\LendCondition;
use App\Domain\Lends\Jobs\SendReturnReminders;
use App\Domain\Requests\Enums\RequestStatus;
use App\Models\Lend;
use App\Models\LearningResource;
use App\Models\Offer;
use App\Models\Request;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;
use Database\Seeders\SeaitSubjectsSeeder;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);
});

function lendTestSubject(): Subject
{
    return Subject::where('code', 'IT 211')->firstOrFail();
}

function lendTestResource(User $owner, ResourceType $type = ResourceType::Textbook): LearningResource
{
    return LearningResource::factory()->create([
        'owner_user_id' => $owner->id,
        'school_id' => $owner->school_id,
        'subject_id' => lendTestSubject()->id,
        'program_id' => $owner->program_id,
        'type' => $type,
        'availability' => ResourceAvailability::Available,
        'lend_count' => 0,
    ]);
}

it('records a lend for a matched request with a physical resource', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();

    $resource = lendTestResource($offerer, ResourceType::Textbook);

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => lendTestSubject()->id,
        'status' => RequestStatus::Matched,
    ]);

    $offer = Offer::create([
        'request_id' => $request->id,
        'offerer_user_id' => $offerer->id,
        'resource_id' => $resource->id,
        'status' => 'accepted',
    ]);

    $request->update(['fulfilled_offer_id' => $offer->id]);

    $returnBy = now()->addDays(14)->format('Y-m-d');
    $lend = (new RecordLend)->handle($request, $offer, $requester, $returnBy);

    expect($lend)->toBeInstanceOf(Lend::class);
    expect($lend->resource_id)->toBe($resource->id);
    expect($lend->offer_id)->toBe($offer->id);
    expect($lend->request_id)->toBe($request->id);
    expect($lend->from_user_id)->toBe($offerer->id);
    expect($lend->to_user_id)->toBe($requester->id);
    expect($lend->return_by->format('Y-m-d'))->toBe($returnBy);
    expect($lend->returned_at)->toBeNull();

    expect($resource->refresh()->availability)->toBe(ResourceAvailability::LentOut);
    expect($resource->refresh()->lend_count)->toBe(1);
    expect($request->refresh()->status)->toBe(RequestStatus::Fulfilled);
});

it('refuses to record a lend for a non-matched request', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();

    $resource = lendTestResource($offerer);

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => lendTestSubject()->id,
        'status' => RequestStatus::Open,
    ]);

    $offer = Offer::create([
        'request_id' => $request->id,
        'offerer_user_id' => $offerer->id,
        'resource_id' => $resource->id,
        'status' => 'pending',
    ]);

    expect(fn () => (new RecordLend)->handle($request, $offer, $requester))
        ->toThrow(RuntimeException::class);
});

it('refuses to record a lend for an offer without a resource', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => lendTestSubject()->id,
        'status' => RequestStatus::Matched,
    ]);

    $offer = Offer::create([
        'request_id' => $request->id,
        'offerer_user_id' => $offerer->id,
        'resource_id' => null,
        'status' => 'accepted',
    ]);

    $request->update(['fulfilled_offer_id' => $offer->id]);

    expect(fn () => (new RecordLend)->handle($request, $offer, $requester))
        ->toThrow(RuntimeException::class);
});

it('refuses to record a duplicate lend for the same offer', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();

    $resource = lendTestResource($offerer);

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => lendTestSubject()->id,
        'status' => RequestStatus::Matched,
    ]);

    $offer = Offer::create([
        'request_id' => $request->id,
        'offerer_user_id' => $offerer->id,
        'resource_id' => $resource->id,
        'status' => 'accepted',
    ]);

    $request->update(['fulfilled_offer_id' => $offer->id]);

    (new RecordLend)->handle($request, $offer, $requester);

    expect(fn () => (new RecordLend)->handle($request, $offer, $requester))
        ->toThrow(RuntimeException::class);
});

it('returns a borrowed resource and marks availability as available', function () {
    $borrower = User::factory()->onboarded()->create();
    $lender = User::factory()->onboarded()->create();

    $resource = lendTestResource($lender, ResourceType::Textbook);
    $resource->update(['availability' => ResourceAvailability::LentOut]);

    $lend = Lend::create([
        'resource_id' => $resource->id,
        'from_user_id' => $lender->id,
        'to_user_id' => $borrower->id,
        'lent_at' => now(),
        'return_by' => now()->addDays(14),
    ]);

    (new ReturnResource)->handle($borrower, $lend, LendCondition::Good);

    expect($lend->refresh()->isReturned())->toBeTrue();
    expect($lend->refresh()->condition_on_return)->toBe(LendCondition::Good);
    expect($resource->refresh()->availability)->toBe(ResourceAvailability::Available);
});

it('refuses to return resource if not the borrower', function () {
    $borrower = User::factory()->onboarded()->create();
    $otherUser = User::factory()->onboarded()->create();
    $lender = User::factory()->onboarded()->create();

    $resource = lendTestResource($lender);

    $lend = Lend::create([
        'resource_id' => $resource->id,
        'from_user_id' => $lender->id,
        'to_user_id' => $borrower->id,
        'lent_at' => now(),
    ]);

    expect(fn () => (new ReturnResource)->handle($otherUser, $lend))
        ->toThrow(RuntimeException::class);
});

it('refuses to return an already returned resource', function () {
    $borrower = User::factory()->onboarded()->create();
    $lender = User::factory()->onboarded()->create();

    $resource = lendTestResource($lender);

    $lend = Lend::create([
        'resource_id' => $resource->id,
        'from_user_id' => $lender->id,
        'to_user_id' => $borrower->id,
        'lent_at' => now(),
        'returned_at' => now(),
    ]);

    expect(fn () => (new ReturnResource)->handle($borrower, $lend))
        ->toThrow(RuntimeException::class);
});

it('detects overdue lends', function () {
    $borrower = User::factory()->onboarded()->create();
    $lender = User::factory()->onboarded()->create();

    $resource = lendTestResource($lender);

    $lend = Lend::create([
        'resource_id' => $resource->id,
        'from_user_id' => $lender->id,
        'to_user_id' => $borrower->id,
        'lent_at' => now()->subDays(10),
        'return_by' => now()->subDays(3)->format('Y-m-d'),
    ]);

    expect($lend->isOverdue())->toBeTrue();
    expect($lend->isReturned())->toBeFalse();
});

it('detects returned lends', function () {
    $borrower = User::factory()->onboarded()->create();
    $lender = User::factory()->onboarded()->create();

    $resource = lendTestResource($lender);

    $lend = Lend::create([
        'resource_id' => $resource->id,
        'from_user_id' => $lender->id,
        'to_user_id' => $borrower->id,
        'lent_at' => now(),
        'returned_at' => now(),
    ]);

    expect($lend->isReturned())->toBeTrue();
    expect($lend->isOverdue())->toBeFalse();
});

it('detects lends due soon', function () {
    $borrower = User::factory()->onboarded()->create();
    $lender = User::factory()->onboarded()->create();

    $resource = lendTestResource($lender);

    $soon = Lend::create([
        'resource_id' => $resource->id,
        'from_user_id' => $lender->id,
        'to_user_id' => $borrower->id,
        'lent_at' => now(),
        'return_by' => now()->addDays(1)->format('Y-m-d'),
    ]);

    $far = Lend::create([
        'resource_id' => $resource->id,
        'from_user_id' => $lender->id,
        'to_user_id' => $borrower->id,
        'lent_at' => now(),
        'return_by' => now()->addDays(14)->format('Y-m-d'),
    ]);

    expect($soon->isDueSoon())->toBeTrue();
    expect($far->isDueSoon())->toBeFalse();
});

it('dispatches return reminder notification for lends due within 2 days', function () {
    Notification::fake();

    $borrower = User::factory()->onboarded()->create();
    $lender = User::factory()->onboarded()->create();

    $resource = lendTestResource($lender);

    $lend = Lend::create([
        'resource_id' => $resource->id,
        'from_user_id' => $lender->id,
        'to_user_id' => $borrower->id,
        'lent_at' => now(),
        'return_by' => now()->addDay()->format('Y-m-d'),
    ]);

    (new SendReturnReminders)->handle();

    Notification::assertSentTo($borrower, \App\Domain\Lends\Notifications\ReturnReminder::class);
});

it('does not dispatch return reminder for already returned lends', function () {
    Notification::fake();

    $borrower = User::factory()->onboarded()->create();
    $lender = User::factory()->onboarded()->create();

    $resource = lendTestResource($lender);

    Lend::create([
        'resource_id' => $resource->id,
        'from_user_id' => $lender->id,
        'to_user_id' => $borrower->id,
        'lent_at' => now(),
        'return_by' => now()->addDay()->format('Y-m-d'),
        'returned_at' => now(),
    ]);

    (new SendReturnReminders)->handle();

    Notification::assertNothingSent();
});

it('renders the lends index page', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->get(route('lends.index'))
        ->assertOk()
        ->assertSee('My Lends');
});

it('records a lend via the record route', function () {
    $requester = User::factory()->onboarded()->create();
    $offerer = User::factory()->onboarded()->create();

    $resource = lendTestResource($offerer, ResourceType::Textbook);

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => lendTestSubject()->id,
        'status' => RequestStatus::Matched,
    ]);

    $offer = Offer::create([
        'request_id' => $request->id,
        'offerer_user_id' => $offerer->id,
        'resource_id' => $resource->id,
        'status' => 'accepted',
    ]);

    $request->update(['fulfilled_offer_id' => $offer->id]);

    $this->actingAs($requester)
        ->post(route('lends.record', [$request, $offer]), [
            'return_by' => now()->addDays(14)->format('Y-m-d'),
        ])
        ->assertRedirect(route('lends.index'));

    expect(Lend::where('offer_id', $offer->id)->exists())->toBeTrue();
});

it('returns a resource via the return route', function () {
    $borrower = User::factory()->onboarded()->create();
    $lender = User::factory()->onboarded()->create();

    $resource = lendTestResource($lender, ResourceType::Textbook);
    $resource->update(['availability' => ResourceAvailability::LentOut]);

    $lend = Lend::create([
        'resource_id' => $resource->id,
        'from_user_id' => $lender->id,
        'to_user_id' => $borrower->id,
        'lent_at' => now(),
        'return_by' => now()->addDays(14),
    ]);

    $this->actingAs($borrower)
        ->post(route('lends.return', $lend), [
            'condition' => 'good',
        ])
        ->assertRedirect(route('lends.index'));

    expect($lend->refresh()->isReturned())->toBeTrue();
    expect($lend->refresh()->condition_on_return)->toBe(LendCondition::Good);
});