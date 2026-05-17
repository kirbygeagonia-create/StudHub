<?php

use App\Domain\Catalog\Actions\ToggleShelfItem;
use App\Models\LearningResource;
use App\Models\Shelf;
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

it('creates a default shelf on first save and adds the resource', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'school_id' => $user->school_id,
        'owner_user_id' => $user->id,
        'subject_id' => $subject->id,
        'program_id' => $user->program_id,
    ]);

    $saved = (new ToggleShelfItem)->handle($user, $resource);

    expect($saved)->toBeTrue();
    expect(Shelf::where('user_id', $user->id)->count())->toBe(1);
    expect($resource->refresh()->save_count)->toBe(1);

    $shelf = Shelf::where('user_id', $user->id)->first();
    expect($shelf->resources()->count())->toBe(1);
});

it('toggles a resource off the shelf and decrements save_count', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'school_id' => $user->school_id,
        'owner_user_id' => $user->id,
        'subject_id' => $subject->id,
        'program_id' => $user->program_id,
    ]);

    (new ToggleShelfItem)->handle($user, $resource);
    expect($resource->refresh()->save_count)->toBe(1);

    $saved = (new ToggleShelfItem)->handle($user, $resource);
    expect($saved)->toBeFalse();
    expect($resource->refresh()->save_count)->toBe(0);

    $shelf = $user->shelves()->first();
    expect($shelf->resources()->count())->toBe(0);
});

it('reports isSaved correctly', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'school_id' => $user->school_id,
        'owner_user_id' => $user->id,
        'subject_id' => $subject->id,
        'program_id' => $user->program_id,
    ]);

    $toggle = new ToggleShelfItem;

    expect($toggle->isSaved($user, $resource))->toBeFalse();

    $toggle->handle($user, $resource);
    expect($toggle->isSaved($user, $resource))->toBeTrue();

    $toggle->handle($user, $resource);
    expect($toggle->isSaved($user, $resource))->toBeFalse();
});

it('reuses the existing shelf on subsequent saves', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource1 = LearningResource::factory()->create([
        'school_id' => $user->school_id,
        'owner_user_id' => $user->id,
        'subject_id' => $subject->id,
        'program_id' => $user->program_id,
    ]);
    $resource2 = LearningResource::factory()->create([
        'school_id' => $user->school_id,
        'owner_user_id' => $user->id,
        'subject_id' => $subject->id,
        'program_id' => $user->program_id,
    ]);

    $toggle = new ToggleShelfItem;

    $toggle->handle($user, $resource1);
    $toggle->handle($user, $resource2);

    expect(Shelf::where('user_id', $user->id)->count())->toBe(1);
    expect(Shelf::where('user_id', $user->id)->first()->resources()->count())->toBe(2);
});

it('renders the shelf page for an onboarded user', function () {
    $user = User::factory()->onboarded()->create();

    $this->actingAs($user)
        ->get('/my-shelf')
        ->assertOk()
        ->assertSee('My Shelf');
});

it('shows saved resources on the shelf page', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'school_id' => $user->school_id,
        'owner_user_id' => $user->id,
        'subject_id' => $subject->id,
        'program_id' => $user->program_id,
        'title' => 'Saved DSA reviewer',
    ]);

    (new ToggleShelfItem)->handle($user, $resource);

    $this->actingAs($user)
        ->get('/my-shelf')
        ->assertOk()
        ->assertSee('Saved DSA reviewer');
});

it('toggle-save route saves a resource and redirects back', function () {
    $user = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::factory()->create([
        'school_id' => $user->school_id,
        'owner_user_id' => $user->id,
        'subject_id' => $subject->id,
        'program_id' => $user->program_id,
    ]);

    $this->actingAs($user)
        ->post(route('resources.toggle-save', $resource))
        ->assertRedirect();

    expect($resource->refresh()->save_count)->toBe(1);
});