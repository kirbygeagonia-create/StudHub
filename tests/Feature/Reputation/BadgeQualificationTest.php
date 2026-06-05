<?php

use App\Domain\Reputation\Actions\CheckAndAwardBadges;
use App\Domain\Reputation\Enums\Badge;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\LearningResource;
use App\Models\Lend;
use App\Models\Offer;
use App\Models\ResourceRequest;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\SeaitSchoolSeeder;
use Database\Seeders\SeaitSubjectsSeeder;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);
});

it('awards PageTurner badge for uploading first resource', function () {
    $user = User::factory()->onboarded()->create();
    $subject = Subject::first();

    LearningResource::factory()->create([
        'owner_user_id' => $user->id,
        'subject_id' => $subject->id,
        'school_id' => $user->school_id,
    ]);

    $action = app(CheckAndAwardBadges::class);
    $action->handle($user, 'upload');

    expect($user->badges()->where('badge', Badge::PageTurner)->exists())->toBeTrue();
});

it('awards Chatterbox badge for 50+ messages', function () {
    $user = User::factory()->onboarded()->create();
    $room = ChatRoom::factory()->create([
        'school_id' => $user->school_id,
        'program_id' => $user->program_id,
    ]);

    for ($i = 0; $i < 50; $i++) {
        ChatMessage::factory()->create([
            'chat_room_id' => $room->id,
            'sender_id' => $user->id,
        ]);
    }

    $action = app(CheckAndAwardBadges::class);
    $action->handle($user, 'community');

    expect($user->badges()->where('badge', Badge::Chatterbox)->exists())->toBeTrue();
});

it('does not award Chatterbox for fewer than 50 messages', function () {
    $user = User::factory()->onboarded()->create();
    $room = ChatRoom::factory()->create([
        'school_id' => $user->school_id,
        'program_id' => $user->program_id,
    ]);

    for ($i = 0; $i < 49; $i++) {
        ChatMessage::factory()->create([
            'chat_room_id' => $room->id,
            'sender_id' => $user->id,
        ]);
    }

    $action = app(CheckAndAwardBadges::class);
    $action->handle($user, 'community');

    expect($user->badges()->where('badge', Badge::Chatterbox)->exists())->toBeFalse();
});

it('awards GenerousSoul badge for first lend', function () {
    $user = User::factory()->onboarded()->create();

    Lend::factory()->create([
        'from_user_id' => $user->id,
    ]);

    $action = app(CheckAndAwardBadges::class);
    $action->handle($user, 'lend');

    expect($user->badges()->where('badge', Badge::GenerousSoul)->exists())->toBeTrue();
});

it('awards Completionist badge when profile is fully filled', function () {
    $user = User::factory()->onboarded()->create([
        'avatar_url' => 'https://example.com/avatar.png',
    ]);

    $action = app(CheckAndAwardBadges::class);
    $action->handle($user, 'special');

    expect($user->badges()->where('badge', Badge::Completionist)->exists())->toBeTrue();
});

it('awards FirstResponder badge for first accepted offer', function () {
    $user = User::factory()->onboarded()->create();
    $requester = User::factory()->onboarded()->create();
    $subject = Subject::first();

    $resourceRequest = ResourceRequest::factory()->create([
        'requester_user_id' => $requester->id,
        'subject_id' => $subject->id,
    ]);

    Offer::factory()->create([
        'request_id' => $resourceRequest->id,
        'offerer_user_id' => $user->id,
        'status' => 'accepted',
    ]);

    $action = app(CheckAndAwardBadges::class);
    $action->handle($user, 'fulfill');

    expect($user->badges()->where('badge', Badge::FirstResponder)->exists())->toBeTrue();
});
