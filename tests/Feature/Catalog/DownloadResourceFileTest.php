<?php

use App\Domain\Catalog\Actions\DownloadResourceFile;
use App\Models\LearningResource;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\SeaitSchoolSeeder;
use Database\Seeders\SeaitSubjectsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);
});

it('throws 404 when resource has no file', function () {
    $user = User::factory()->onboarded()->create();
    $subject = Subject::first();

    $resource = LearningResource::factory()->create([
        'owner_user_id' => $user->id,
        'subject_id' => $subject->id,
        'school_id' => $user->school_id,
        'file_url' => null,
    ]);

    $action = app(DownloadResourceFile::class);

    $action->handle($user, $resource);
})->throws(NotFoundHttpException::class);

it('increments download_count when downloading', function () {
    $user = User::factory()->onboarded()->create();
    $subject = Subject::first();
    Storage::fake('public');

    $file = UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf');
    $path = Storage::disk('public')->putFile('resources', $file);

    $resource = LearningResource::factory()->create([
        'owner_user_id' => $user->id,
        'subject_id' => $subject->id,
        'school_id' => $user->school_id,
        'file_url' => $path,
        'file_mime' => 'application/pdf',
        'is_watermarked' => false,
    ]);

    $originalCount = $resource->download_count;

    $action = app(DownloadResourceFile::class);
    $response = $action->handle($user, $resource);

    expect($response->getStatusCode())->toBe(200);
    expect($resource->fresh()->download_count)->toBe($originalCount + 1);
});
