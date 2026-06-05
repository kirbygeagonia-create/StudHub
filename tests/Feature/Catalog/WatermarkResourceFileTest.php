<?php

use App\Domain\Catalog\Jobs\WatermarkResourceFile;
use App\Models\LearningResource;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\SeaitSchoolSeeder;
use Database\Seeders\SeaitSubjectsSeeder;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);
});

it('dispatches the watermark job when a resource is created with a file', function () {
    Queue::fake();

    $user = User::factory()->onboarded()->create();
    $subject = Subject::first();

    $resource = LearningResource::factory()->create([
        'owner_user_id' => $user->id,
        'subject_id' => $subject->id,
        'school_id' => $user->school_id,
        'file_url' => 'resources/test.pdf',
        'file_mime' => 'application/pdf',
    ]);

    WatermarkResourceFile::dispatch($resource->id);

    Queue::assertPushed(WatermarkResourceFile::class, function ($job) use ($resource) {
        return $job->resourceId === $resource->id;
    });
});

it('has a unique ID based on the resource ID', function () {
    $job = new WatermarkResourceFile(42);

    expect($job->uniqueId())->toBe('42');
});

it('has the correct retry and timeout configuration', function () {
    $job = new WatermarkResourceFile(1);

    expect($job->tries)->toBe(2);
    expect($job->backoff)->toBe(30);
    expect($job->timeout)->toBe(60);
});
