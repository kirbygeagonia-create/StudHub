<?php

use App\Domain\Requests\Enums\RequestUrgency;
use App\Domain\Requests\Jobs\NotifyRoutedUsers;
use App\Domain\Requests\Notifications\RequestRoutedNotification;
use App\Models\Program;
use App\Models\ResourceRequest;
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

    $this->subject = Subject::first();
    $this->program = Program::first();
});

it('respects only_urgent preference and skips non-urgent requests', function () {
    Notification::fake();

    $user = User::factory()->onboarded()->create([
        'notification_preferences' => ['only_urgent' => true, 'muted_programs' => []],
    ]);

    $request = ResourceRequest::factory()->create([
        'urgency' => RequestUrgency::Normal,
    ]);

    $job = new NotifyRoutedUsers($request->id, [$user->id]);
    $job->handle();

    Notification::assertNothingSent();
});

it('notifies user with only_urgent when request is urgent', function () {
    Notification::fake();

    $user = User::factory()->onboarded()->create([
        'notification_preferences' => ['only_urgent' => true, 'muted_programs' => []],
    ]);

    $request = ResourceRequest::factory()->create([
        'urgency' => RequestUrgency::Urgent,
    ]);

    $job = new NotifyRoutedUsers($request->id, [$user->id]);
    $job->handle();

    Notification::assertSentTo($user, RequestRoutedNotification::class);
});

it('respects muted_programs preference', function () {
    Notification::fake();

    $user = User::factory()->onboarded()->create([
        'program_id' => $this->program->id,
        'notification_preferences' => ['only_urgent' => false, 'muted_programs' => [$this->program->id]],
    ]);

    $request = ResourceRequest::factory()->create([
        'urgency' => RequestUrgency::Normal,
    ]);

    $job = new NotifyRoutedUsers($request->id, [$user->id]);
    $job->handle();

    Notification::assertNothingSent();
});
