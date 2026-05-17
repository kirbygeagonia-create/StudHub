<?php

use App\Domain\Catalog\Actions\CreateResource;
use App\Domain\Catalog\Actions\DownloadResourceFile;
use App\Domain\Catalog\Actions\ToggleShelfItem;
use App\Domain\Catalog\Enums\ResourceType;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;
use Database\Seeders\SeaitSubjectsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);
});

it('downloads the original file for non-PDF resources', function () {
    $user = User::factory()->onboarded()->create();
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    Storage::fake('public');

    $file = UploadedFile::fake()->create('notes.txt', 100, 'text/plain');
    $resource = (new CreateResource)->handle($user, [
        'subject_id' => $subject->id,
        'type' => ResourceType::LectureNotes->value,
        'title' => 'Plain text notes',
    ], $file);

    $response = $this->actingAs($user)
        ->get(route('resources.download', $resource));

    $response->assertOk();
});

it('serves the download route for authenticated users', function () {
    $user = User::factory()->onboarded()->create();
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    Storage::fake('public');

    $file = UploadedFile::fake()->create('reviewer.pdf', 200, 'application/pdf');
    $resource = (new CreateResource)->handle($user, [
        'subject_id' => $subject->id,
        'type' => ResourceType::Reviewer->value,
        'title' => 'DSA reviewer',
    ], $file);

    $this->actingAs($user)
        ->get(route('resources.download', $resource))
        ->assertOk();
});

it('returns 404 for resources without a file', function () {
    $user = User::factory()->onboarded()->create();
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    $resource = (new CreateResource)->handle($user, [
        'subject_id' => $subject->id,
        'type' => ResourceType::Reviewer->value,
        'title' => 'No file attached',
    ]);

    $this->actingAs($user)
        ->get(route('resources.download', $resource))
        ->assertNotFound();
});

it('redirects unauthenticated users from download route', function () {
    $user = User::factory()->onboarded()->create();
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    Storage::fake('public');

    $file = UploadedFile::fake()->create('r.pdf', 100, 'application/pdf');
    $resource = (new CreateResource)->handle($user, [
        'subject_id' => $subject->id,
        'type' => ResourceType::Reviewer->value,
        'title' => 'Reviewer',
    ], $file);

    $this->get(route('resources.download', $resource))
        ->assertRedirect(route('login'));
});

it('increments save_count when a resource is saved via the route', function () {
    $user = User::factory()->onboarded()->create();
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = (new CreateResource)->handle($user, [
        'subject_id' => $subject->id,
        'type' => ResourceType::Reviewer->value,
        'title' => 'Reviewer with saves',
    ]);

    expect($resource->refresh()->save_count)->toBe(0);

    $this->actingAs($user)
        ->post(route('resources.toggle-save', $resource))
        ->assertRedirect();

    expect($resource->refresh()->save_count)->toBe(1);
});