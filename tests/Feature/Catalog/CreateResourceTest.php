<?php

use App\Domain\Catalog\Actions\CreateResource;
use App\Domain\Catalog\Enums\ResourceAvailability;
use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Catalog\Enums\ResourceVisibility;
use App\Domain\Catalog\Jobs\WatermarkResourceFile;
use App\Models\LearningResource;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;
use Database\Seeders\SeaitSubjectsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);
});

it('creates a resource and stamps it with owner / school / program from the user', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    Bus::fake();
    Storage::fake('public');

    $action = new CreateResource;
    $resource = $action->handle($user, [
        'subject_id' => $subject->id,
        'type' => ResourceType::Reviewer,
        'title' => 'DSA midterm reviewer',
        'description' => 'sorting + trees',
        'availability' => ResourceAvailability::Available,
        'visibility' => ResourceVisibility::School,
    ]);

    expect($resource)->toBeInstanceOf(LearningResource::class);
    expect($resource->school_id)->toBe($user->school_id);
    expect($resource->owner_user_id)->toBe($user->id);
    expect($resource->program_id)->toBe($user->program_id);
    expect($resource->subject_id)->toBe($subject->id);
    expect($resource->type)->toBe(ResourceType::Reviewer);
    expect($resource->availability)->toBe(ResourceAvailability::Available);
    expect($resource->visibility)->toBe(ResourceVisibility::School);
    expect($resource->published_at)->not->toBeNull();
});

it('stores an uploaded file on the public disk and queues the watermark job', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    Bus::fake();
    Storage::fake('public');

    $file = UploadedFile::fake()->create('reviewer.pdf', 200, 'application/pdf');

    $resource = (new CreateResource)->handle($user, [
        'subject_id' => $subject->id,
        'type' => ResourceType::Reviewer->value,
        'title' => 'DSA midterm reviewer',
    ], $file);

    expect($resource->file_url)->not->toBeNull();
    expect($resource->file_mime)->toBe('application/pdf');
    Storage::disk('public')->assertExists($resource->file_url);
    Bus::assertDispatched(WatermarkResourceFile::class, fn ($job) => $job->resourceId === $resource->id);
});

it('does not queue the watermark job when no file is attached', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    Bus::fake();

    (new CreateResource)->handle($user, [
        'subject_id' => $subject->id,
        'type' => ResourceType::Reviewer->value,
        'title' => 'No file attached',
    ]);

    Bus::assertNotDispatched(WatermarkResourceFile::class);
});

it('refuses to create a resource for a subject in another school', function () {
    $user = User::factory()->onboarded()->create();
    $otherSchool = School::create([
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

    expect(fn () => (new CreateResource)->handle($user, [
        'subject_id' => $foreignSubject->id,
        'type' => ResourceType::Reviewer->value,
        'title' => 'Cannot post',
    ]))->toThrow(RuntimeException::class);
});

it('flips is_watermarked when the watermark job is run synchronously', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    Storage::fake('public');

    $resource = (new CreateResource)->handle($user, [
        'subject_id' => $subject->id,
        'type' => ResourceType::Reviewer->value,
        'title' => 'Reviewer',
    ], UploadedFile::fake()->create('r.pdf', 50, 'application/pdf'));

    expect($resource->is_watermarked)->toBeFalse();

    (new WatermarkResourceFile($resource->id))->handle();

    expect($resource->refresh()->is_watermarked)->toBeTrue();
});
