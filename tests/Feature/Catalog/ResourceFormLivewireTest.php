<?php

use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Catalog\Jobs\WatermarkResourceFile;
use App\Livewire\Resources\ResourceForm;
use App\Models\LearningResource;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;
use Database\Seeders\SeaitSubjectsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);
});

it('rejects an empty title', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();

    Livewire::actingAs($user)
        ->test(ResourceForm::class)
        ->set('subject_id', $subject->id)
        ->set('title', '   ')
        ->call('save')
        ->assertHasErrors(['title']);

    expect(LearningResource::count())->toBe(0);
});

it('creates a resource via the Livewire form and queues the watermark job', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    Bus::fake();
    Storage::fake('public');

    Livewire::actingAs($user)
        ->test(ResourceForm::class)
        ->set('subject_id', $subject->id)
        ->set('type', ResourceType::Reviewer->value)
        ->set('title', 'DSA reviewer (sorting)')
        ->set('description', 'Covers bubble, merge, and quick sort.')
        ->set('file', UploadedFile::fake()->create('reviewer.pdf', 250, 'application/pdf'))
        ->call('save');

    expect(LearningResource::count())->toBe(1);
    /** @var LearningResource $resource */
    $resource = LearningResource::first();
    expect($resource->title)->toBe('DSA reviewer (sorting)');
    expect($resource->owner_user_id)->toBe($user->id);
    expect($resource->file_url)->not->toBeNull();
    Bus::assertDispatched(WatermarkResourceFile::class);
});

it('rejects a file with a disallowed mimetype', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    Storage::fake('public');

    Livewire::actingAs($user)
        ->test(ResourceForm::class)
        ->set('subject_id', $subject->id)
        ->set('title', 'Bad mime')
        ->set('file', UploadedFile::fake()->create('payload.bin', 50, 'application/octet-stream'))
        ->call('save')
        ->assertHasErrors(['file']);

    expect(LearningResource::count())->toBe(0);
});
