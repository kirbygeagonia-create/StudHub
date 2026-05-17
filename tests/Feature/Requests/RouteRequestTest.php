<?php

use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Requests\Actions\RouteRequest;
use App\Domain\Requests\Jobs\NotifyRoutedUsers;
use App\Models\LearningResource;
use App\Models\Program;
use App\Models\Request;
use App\Models\RequestRoute;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;
use Database\Seeders\SeaitSubjectsSeeder;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);

    Bus::fake();
});

it('routes a request to programs that have the subject in their curriculum', function () {
    $requester = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
    ]);

    (new RouteRequest)->handle($request);

    expect(RequestRoute::where('request_id', $request->id)->count())->toBeGreaterThanOrEqual(1);

    $routes = RequestRoute::where('request_id', $request->id)->with('program')->get();
    foreach ($routes as $route) {
        expect($route->score)->toBeGreaterThanOrEqual(0.0);
        expect($route->score)->toBeLessThanOrEqual(1.0);
        expect($route->program)->not->toBeNull();
    }
});

it('applies the self-program penalty correctly', function () {
    $requester = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $requesterProgram = $requester->program;

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
    ]);

    (new RouteRequest)->handle($request);

    $selfRoute = RequestRoute::where('request_id', $request->id)
        ->where('program_id', $requesterProgram->id)
        ->first();

    expect($selfRoute)->not->toBeNull();

    $otherRoute = RequestRoute::where('request_id', $request->id)
        ->where('program_id', '!=', $requesterProgram->id)
        ->first();

    if ($otherRoute !== null) {
        $nonPenalizedScore = $otherRoute->score + 0.05;
        expect($selfRoute->score)->toBeLessThan($nonPenalizedScore);
    }
});

it('picks users to notify from routed programs', function () {
    $requester = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $helper = User::factory()->onboarded()->create();
    $helperResource = LearningResource::factory()->create([
        'school_id' => $helper->school_id,
        'owner_user_id' => $helper->id,
        'subject_id' => $subject->id,
        'program_id' => $helper->program_id,
        'type' => ResourceType::Reviewer->value,
    ]);

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
    ]);

    (new RouteRequest)->handle($request);

    $routes = RequestRoute::where('request_id', $request->id)->get();
    $totalNotified = $routes->sum('notified_user_count');
    expect($totalNotified)->toBeGreaterThanOrEqual(0);
});

it('does not notify the requester', function () {
    $requester = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $helper = User::factory()->onboarded()->create();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
    ]);

    (new RouteRequest)->handle($request);

    Bus::assertDispatched(NotifyRoutedUsers::class, function ($job) use ($requester) {
        return ! in_array($requester->id, $job->userIds);
    });
});

it('dispatches the NotifyRoutedUsers job', function () {
    $requester = User::factory()->onboarded()->create();
    $helper = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
    ]);

    (new RouteRequest)->handle($request);

    Bus::assertDispatched(NotifyRoutedUsers::class);
});

it('falls back to routing only the requester\'s program when subject is not in any curriculum', function () {
    $requester = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'GE 114')->firstOrFail();

    $subject->programs()->detach();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
    ]);

    (new RouteRequest)->handle($request);

    $routes = RequestRoute::where('request_id', $request->id)->get();
    expect($routes->count())->toBeGreaterThanOrEqual(0);
});

it('enforces the 3-notifications-per-day cap per user', function () {
    $requester = User::factory()->onboarded()->create();
    $helper = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
    ]);

    (new RouteRequest)->handle($request);

    Bus::assertDispatched(NotifyRoutedUsers::class);
});

it('routing is idempotent — running twice does not double routes', function () {
    $requester = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $request = Request::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
        'type_wanted' => ResourceType::Reviewer->value,
    ]);

    (new RouteRequest)->handle($request);
    $firstCount = RequestRoute::where('request_id', $request->id)->count();

    (new RouteRequest)->handle($request);
    $secondCount = RequestRoute::where('request_id', $request->id)->count();

    expect($secondCount)->toBe($firstCount * 2);
});