<?php

use App\Domain\Catalog\Enums\ResourceAvailability;
use App\Domain\Identity\Enums\UserRole;
use App\Domain\Moderation\Actions\CreateReport;
use App\Domain\Moderation\Actions\LogAudit;
use App\Domain\Moderation\Actions\ResolveReport;
use App\Domain\Moderation\Actions\SuspendUser;
use App\Domain\Moderation\Enums\ReportedType;
use App\Domain\Moderation\Enums\ReportStatus;
use App\Models\AuditLog;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\LearningResource;
use App\Models\ProgramModerator;
use App\Models\Report;
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

it('creates a report on a resource', function () {
    $reporter = User::factory()->onboarded()->create();
    $owner = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'owner_user_id' => $owner->id,
        'school_id' => $owner->school_id,
        'subject_id' => $subject->id,
        'program_id' => $owner->program_id,
    ]);

    $report = (new CreateReport)->handle($reporter, ReportedType::Resource, $resource->id, 'spam', 'test notes');

    expect($report)->toBeInstanceOf(Report::class);
    expect($report->reporter_user_id)->toBe($reporter->id);
    expect($report->reported_type)->toBe(ReportedType::Resource);
    expect($report->reported_id)->toBe($resource->id);
    expect($report->reason)->toBe('spam');
    expect($report->notes)->toBe('test notes');
    expect($report->isOpen())->toBeTrue();
});

it('creates a report on a message', function () {
    $reporter = User::factory()->onboarded()->create();
    $sender = User::factory()->onboarded()->create();

    $room = ChatRoom::create([
        'program_id' => $sender->program_id,
        'school_id' => $sender->school_id,
        'kind' => 'program',
        'title' => 'Test Room',
        'slug' => 'test-room',
    ]);

    $message = ChatMessage::create([
        'chat_room_id' => $room->id,
        'sender_id' => $sender->id,
        'body' => 'test message',
    ]);

    $report = (new CreateReport)->handle($reporter, ReportedType::Message, $message->id, 'harassment');

    expect($report)->toBeInstanceOf(Report::class);
    expect($report->reported_type)->toBe(ReportedType::Message);
    expect($report->reported_id)->toBe($message->id);
});

it('creates a report on a user', function () {
    $reporter = User::factory()->onboarded()->create();
    $target = User::factory()->onboarded()->create();

    $report = (new CreateReport)->handle($reporter, ReportedType::User, $target->id, 'inappropriate');

    expect($report->reported_type)->toBe(ReportedType::User);
    expect($report->reported_id)->toBe($target->id);
});

it('prevents duplicate reports from the same user', function () {
    $reporter = User::factory()->onboarded()->create();
    $target = User::factory()->onboarded()->create();

    (new CreateReport)->handle($reporter, ReportedType::User, $target->id, 'spam');

    expect(fn () => (new CreateReport)->handle($reporter, ReportedType::User, $target->id, 'harassment'))
        ->toThrow(RuntimeException::class);
});

it('refuses to report a non-existent entity', function () {
    $reporter = User::factory()->onboarded()->create();

    expect(fn () => (new CreateReport)->handle($reporter, ReportedType::Resource, 99999, 'spam'))
        ->toThrow(RuntimeException::class);
});

it('resolves a report as actioned and deducts karma', function () {
    $reporter = User::factory()->onboarded()->create();
    $owner = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'owner_user_id' => $owner->id,
        'school_id' => $owner->school_id,
        'subject_id' => $subject->id,
        'program_id' => $owner->program_id,
    ]);

    $report = (new CreateReport)->handle($reporter, ReportedType::Resource, $resource->id, 'spam');

    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);

    (new ResolveReport)->handle($moderator, $report, ReportStatus::Actioned, 'Content removed');

    expect($report->refresh()->status)->toBe(ReportStatus::Actioned);
    expect($report->refresh()->handled_by_user_id)->toBe($moderator->id);
    expect($report->refresh()->resolution_note)->toBe('Content removed');
    expect($owner->refresh()->karma)->toBe(-5);
});

it('resolves a report as dismissed without deducting karma', function () {
    $reporter = User::factory()->onboarded()->create();
    $owner = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'owner_user_id' => $owner->id,
        'school_id' => $owner->school_id,
        'subject_id' => $subject->id,
        'program_id' => $owner->program_id,
    ]);

    $report = (new CreateReport)->handle($reporter, ReportedType::Resource, $resource->id, 'spam');

    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);

    (new ResolveReport)->handle($moderator, $report, ReportStatus::Dismissed);

    expect($report->refresh()->status)->toBe(ReportStatus::Dismissed);
    expect($owner->refresh()->karma)->toBe(0);
});

it('refuses to resolve an already-resolved report', function () {
    $reporter = User::factory()->onboarded()->create();
    $owner = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'owner_user_id' => $owner->id,
        'school_id' => $owner->school_id,
        'subject_id' => $subject->id,
        'program_id' => $owner->program_id,
    ]);

    $report = (new CreateReport)->handle($reporter, ReportedType::Resource, $resource->id, 'spam');
    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);
    (new ResolveReport)->handle($moderator, $report, ReportStatus::Dismissed);

    expect(fn () => (new ResolveReport)->handle($moderator, $report, ReportStatus::Actioned))
        ->toThrow(RuntimeException::class);
});

it('suspends a user and creates an audit log entry', function () {
    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);
    $target = User::factory()->onboarded()->create();

    (new SuspendUser)->handle($moderator, $target, 7, 'spamming');

    expect($target->refresh()->isSuspended())->toBeTrue();
    expect(AuditLog::where('action', 'user.suspend')->where('target_id', $target->id)->exists())->toBeTrue();
});

it('unsuspends a user and creates an audit log entry', function () {
    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);
    $target = User::factory()->onboarded()->create(['suspended_until' => now()->addDays(7)]);

    (new SuspendUser)->unsuspend($moderator, $target);

    expect($target->refresh()->isSuspended())->toBeFalse();
    expect(AuditLog::where('action', 'user.unsuspend')->where('target_id', $target->id)->exists())->toBeTrue();
});

it('refuses to suspend an admin', function () {
    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);
    $admin = User::factory()->onboarded()->create(['role' => UserRole::Admin]);

    expect(fn () => (new SuspendUser)->handle($moderator, $admin, 7))
        ->toThrow(RuntimeException::class);
});

it('refuses to suspend yourself', function () {
    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);

    expect(fn () => (new SuspendUser)->handle($moderator, $moderator, 7))
        ->toThrow(RuntimeException::class);
});

it('creates an audit log entry', function () {
    $actor = User::factory()->onboarded()->create();

    (new LogAudit)->handle($actor, 'test.action', 'User', $actor->id, ['key' => 'value']);

    $log = AuditLog::first();
    expect($log->actor_user_id)->toBe($actor->id);
    expect($log->action)->toBe('test.action');
    expect($log->target_type)->toBe('User');
    expect($log->target_id)->toBe($actor->id);
    expect($log->metadata)->toBe(['key' => 'value']);
});

it('stores a report via the HTTP route', function () {
    $reporter = User::factory()->onboarded()->create();
    $owner = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'owner_user_id' => $owner->id,
        'school_id' => $owner->school_id,
        'subject_id' => $subject->id,
        'program_id' => $owner->program_id,
    ]);

    $this->actingAs($reporter)
        ->post(route('reports.store'), [
            'reported_type' => 'resource',
            'reported_id' => $resource->id,
            'reason' => 'spam',
        ])
        ->assertRedirect();

    expect(Report::where('reported_type', 'resource')->where('reported_id', $resource->id)->exists())->toBeTrue();
});

it('renders the moderation dashboard for moderators', function () {
    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);

    $this->actingAs($moderator)
        ->get(route('moderation.dashboard'))
        ->assertOk()
        ->assertSee('Moderation Dashboard');
});

it('renders the admin dashboard for admins', function () {
    $admin = User::factory()->onboarded()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Admin Dashboard');
});

it('blocks students from the moderation dashboard', function () {
    $student = User::factory()->onboarded()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->get(route('moderation.dashboard'))
        ->assertForbidden();
});

it('blocks students from the admin dashboard', function () {
    $student = User::factory()->onboarded()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('admin can assign a moderator to a program', function () {
    $admin = User::factory()->onboarded()->create(['role' => UserRole::Admin]);
    $user = User::factory()->onboarded()->create(['role' => UserRole::Student]);
    $program = $user->program;

    $this->actingAs($admin)
        ->post(route('admin.moderators.assign'), [
            'user_id' => $user->id,
            'program_id' => $program->id,
        ])
        ->assertRedirect(route('admin.dashboard'));

    expect(ProgramModerator::where('user_id', $user->id)->exists())->toBeTrue();
    expect($user->refresh()->role)->toBe(UserRole::Moderator);
});

it('admin can remove a moderator from a program', function () {
    $admin = User::factory()->onboarded()->create(['role' => UserRole::Admin]);
    $mod = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);
    $program = $mod->program;

    $moderatorship = ProgramModerator::create([
        'user_id' => $mod->id,
        'program_id' => $program->id,
        'assigned_by_user_id' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.moderators.remove'), [
            'moderator_id' => $moderatorship->id,
        ])
        ->assertRedirect(route('admin.dashboard'));

    expect(ProgramModerator::where('id', $moderatorship->id)->exists())->toBeFalse();
    expect($mod->refresh()->role)->toBe(UserRole::Student);
});

it('admin can suspend a user', function () {
    $admin = User::factory()->onboarded()->create(['role' => UserRole::Admin]);
    $target = User::factory()->onboarded()->create();

    $this->actingAs($admin)
        ->post(route('admin.suspend'), [
            'user_id' => $target->id,
            'days' => 30,
        ])
        ->assertRedirect(route('admin.dashboard'));

    expect($target->refresh()->isSuspended())->toBeTrue();
});

it('admin can unsuspend a user', function () {
    $admin = User::factory()->onboarded()->create(['role' => UserRole::Admin]);
    $target = User::factory()->onboarded()->create(['suspended_until' => now()->addDays(30)]);

    $this->actingAs($admin)
        ->post(route('admin.unsuspend'), [
            'user_id' => $target->id,
        ])
        ->assertRedirect(route('admin.dashboard'));

    expect($target->refresh()->isSuspended())->toBeFalse();
});

it('moderator can resolve a report via HTTP', function () {
    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);
    $reporter = User::factory()->onboarded()->create();
    $owner = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'owner_user_id' => $owner->id,
        'school_id' => $owner->school_id,
        'subject_id' => $subject->id,
        'program_id' => $owner->program_id,
    ]);

    $report = (new CreateReport)->handle($reporter, ReportedType::Resource, $resource->id, 'spam');

    $this->actingAs($moderator)
        ->post(route('moderation.resolve', $report), [
            'resolution' => 'actioned',
            'resolution_note' => 'removed',
        ])
        ->assertRedirect(route('moderation.dashboard'));

    expect($report->refresh()->status)->toBe(ReportStatus::Actioned);
});

it('moderator can suspend a user via HTTP', function () {
    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);
    $target = User::factory()->onboarded()->create();

    $this->actingAs($moderator)
        ->post(route('moderation.suspend'), [
            'user_id' => $target->id,
            'days' => 7,
        ])
        ->assertRedirect(route('moderation.dashboard'));

    expect($target->refresh()->isSuspended())->toBeTrue();
});

it('suspended user is blocked from accessing routes', function () {
    $student = User::factory()->onboarded()->create(['suspended_until' => now()->addDays(7)]);

    $this->actingAs($student)
        ->get(route('dashboard'))
        ->assertForbidden();
});

it('user with expired suspension can access routes', function () {
    $student = User::factory()->onboarded()->create(['suspended_until' => now()->subDay()]);

    expect($student->isSuspended())->toBeFalse();

    $this->actingAs($student)
        ->get(route('dashboard'))
        ->assertOk();
});

it('User model has correct role helper methods', function () {
    $student = User::factory()->make(['role' => UserRole::Student]);
    $moderator = User::factory()->make(['role' => UserRole::Moderator]);
    $admin = User::factory()->make(['role' => UserRole::Admin]);

    expect($student->isStudent())->toBeTrue();
    expect($student->isModerator())->toBeFalse();
    expect($student->isAdmin())->toBeFalse();

    expect($moderator->isModerator())->toBeTrue();
    expect($moderator->isStudent())->toBeFalse();

    expect($admin->isAdmin())->toBeTrue();
    expect($admin->isStudent())->toBeFalse();
});

it('prevents self-report via CreateReport action', function () {
    $user = User::factory()->onboarded()->create();

    expect(fn () => (new CreateReport)->handle($user, ReportedType::User, $user->id, 'spam'))
        ->toThrow(RuntimeException::class);
});

it('prevents reporting own message', function () {
    $user = User::factory()->onboarded()->create();

    $room = ChatRoom::create([
        'program_id' => $user->program_id,
        'school_id' => $user->school_id,
        'kind' => 'program',
        'title' => 'Test Room',
        'slug' => 'test-room-self',
    ]);

    $message = ChatMessage::create([
        'chat_room_id' => $room->id,
        'sender_id' => $user->id,
        'body' => 'my own message',
    ]);

    expect(fn () => (new CreateReport)->handle($user, ReportedType::Message, $message->id, 'spam'))
        ->toThrow(RuntimeException::class);
});

it('prevents reporting own resource', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'owner_user_id' => $user->id,
        'school_id' => $user->school_id,
        'subject_id' => $subject->id,
        'program_id' => $user->program_id,
    ]);

    expect(fn () => (new CreateReport)->handle($user, ReportedType::Resource, $resource->id, 'spam'))
        ->toThrow(RuntimeException::class);
});

it('snapshots message preview into audit log when report actioned on message', function () {
    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);
    $reporter = User::factory()->onboarded()->create();
    $sender = User::factory()->onboarded()->create();

    $room = ChatRoom::create([
        'program_id' => $sender->program_id,
        'school_id' => $sender->school_id,
        'kind' => 'program',
        'title' => 'Snapshot Room',
        'slug' => 'snapshot-room',
    ]);

    $message = ChatMessage::create([
        'chat_room_id' => $room->id,
        'sender_id' => $sender->id,
        'body' => 'this is the inappropriate content',
    ]);

    $report = (new CreateReport)->handle($reporter, ReportedType::Message, $message->id, 'harassment');

    (new ResolveReport)->handle($moderator, $report, ReportStatus::Actioned, 'Hidden message');

    expect(AuditLog::where('action', 'message.hide')->where('target_id', $message->id)->exists())->toBeTrue();
});

it('ResolveReport actioned on resource archives it', function () {
    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);
    $reporter = User::factory()->onboarded()->create();
    $owner = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'owner_user_id' => $owner->id,
        'school_id' => $owner->school_id,
        'subject_id' => $subject->id,
        'program_id' => $owner->program_id,
        'availability' => 'available',
    ]);

    $report = (new CreateReport)->handle($reporter, ReportedType::Resource, $resource->id, 'copyright');

    (new ResolveReport)->handle($moderator, $report, ReportStatus::Actioned, 'Copyright violation');

    expect($resource->refresh()->availability)->toBe(ResourceAvailability::Archived);
    expect(AuditLog::where('action', 'resource.archive')->where('target_id', $resource->id)->exists())->toBeTrue();
});

it('SuspendUser accepts days and reason parameters', function () {
    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);
    $target = User::factory()->onboarded()->create();

    (new SuspendUser)->handle($moderator, $target, 14, 'inappropriate behavior');

    expect($target->refresh()->suspended_until)->not->toBeNull();
    expect($target->refresh()->suspended_until->isFuture())->toBeTrue();
});

it('Report model returns correct status labels', function () {
    $reporter = User::factory()->onboarded()->create();
    $owner = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'owner_user_id' => $owner->id,
        'school_id' => $owner->school_id,
        'subject_id' => $subject->id,
        'program_id' => $owner->program_id,
    ]);

    $report = (new CreateReport)->handle($reporter, ReportedType::Resource, $resource->id, 'spam');

    expect($report->status)->toBe(ReportStatus::Open);
    expect($report->status->label())->toBe('Open');
    expect($report->isOpen())->toBeTrue();

    $moderator = User::factory()->onboarded()->create(['role' => UserRole::Moderator]);
    (new ResolveReport)->handle($moderator, $report, ReportStatus::Dismissed);

    expect($report->refresh()->isOpen())->toBeFalse();
    expect($report->refresh()->status)->toBe(ReportStatus::Dismissed);
});

it('ReportSchoolScope filters reports by reporter school', function () {
    $schoolASeeder = new SeaitSchoolSeeder;
    $schoolASeeder->run();

    $userA = User::factory()->onboarded()->create();
    $reporterA = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resourceA = LearningResource::factory()->create([
        'owner_user_id' => $userA->id,
        'school_id' => $userA->school_id,
        'subject_id' => $subject->id,
        'program_id' => $userA->program_id,
    ]);

    (new CreateReport)->handle($reporterA, ReportedType::Resource, $resourceA->id, 'spam');

    $this->actingAs($reporterA);
    expect(Report::count())->toBe(1);
});

it('non-moderator cannot resolve a report', function () {
    $reporter = User::factory()->onboarded()->create();
    $owner = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'owner_user_id' => $owner->id,
        'school_id' => $owner->school_id,
        'subject_id' => $subject->id,
        'program_id' => $owner->program_id,
    ]);

    $report = (new CreateReport)->handle($reporter, ReportedType::Resource, $resource->id, 'spam');
    $student = User::factory()->onboarded()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->post(route('moderation.resolve', $report), [
            'resolution' => 'actioned',
        ])
        ->assertForbidden();

    expect($report->fresh()->isOpen())->toBeTrue();
});

it('isSuspended returns false for past suspensions', function () {
    $user = User::factory()->onboarded()->create(['suspended_until' => now()->subMinute()]);
    expect($user->isSuspended())->toBeFalse();
});

it('isSuspended returns true for future suspensions', function () {
    $user = User::factory()->onboarded()->create(['suspended_until' => now()->addMinute()]);
    expect($user->isSuspended())->toBeTrue();
});

it('isSuspended returns false when suspended_until is null', function () {
    $user = User::factory()->onboarded()->create(['suspended_until' => null]);
    expect($user->isSuspended())->toBeFalse();
});
