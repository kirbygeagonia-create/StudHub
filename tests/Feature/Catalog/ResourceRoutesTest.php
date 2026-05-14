<?php

use App\Domain\Catalog\Enums\ResourceAvailability;
use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Catalog\Enums\ResourceVisibility;
use App\Models\LearningResource;
use App\Models\Program;
use App\Models\School;
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

it('renders the resources index for an onboarded user', function () {
    $alice = User::factory()->onboarded()->create();

    $this->actingAs($alice)
        ->get('/resources')
        ->assertOk()
        ->assertSee('Resources');
});

it('renders the resource create form', function () {
    $alice = User::factory()->onboarded()->create();

    $this->actingAs($alice)
        ->get('/resources/create')
        ->assertOk()
        ->assertSee('Post a resource');
});

it('renders a resource detail page when accessible', function () {
    $alice = User::factory()->onboarded()->create();
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    $resource = LearningResource::create([
        'school_id' => $alice->school_id,
        'owner_user_id' => $alice->id,
        'subject_id' => $subject->id,
        'program_id' => $alice->program_id,
        'type' => ResourceType::Reviewer->value,
        'title' => 'DSA reviewer',
        'availability' => ResourceAvailability::Available->value,
        'visibility' => ResourceVisibility::School->value,
        'published_at' => now(),
    ]);

    $this->actingAs($alice)
        ->get(route('resources.show', $resource))
        ->assertOk()
        ->assertSee('DSA reviewer');
});

it('blocks cross-program access for program_only resources', function () {
    $alice = User::factory()->onboarded()->create();
    /** @var Program $bsce */
    $bsce = Program::where('code', 'BSCE')->firstOrFail();
    $bob = User::factory()->onboarded()->create([
        'school_id' => $alice->school_id,
        'program_id' => $bsce->id,
        'college_id' => $bsce->college_id,
    ]);
    /** @var Subject $subject */
    $subject = Subject::where('code', 'CE 311')->firstOrFail();
    $resource = LearningResource::create([
        'school_id' => $bob->school_id,
        'owner_user_id' => $bob->id,
        'subject_id' => $subject->id,
        'program_id' => $bob->program_id,
        'type' => ResourceType::Reviewer->value,
        'title' => 'BSCE-only structures reviewer',
        'availability' => ResourceAvailability::Available->value,
        'visibility' => ResourceVisibility::ProgramOnly->value,
        'published_at' => now(),
    ]);

    $this->actingAs($alice)
        ->get(route('resources.show', $resource))
        ->assertForbidden();

    $this->actingAs($bob)
        ->get(route('resources.show', $resource))
        ->assertOk();
});

it('returns 404 when the resource belongs to a different school', function () {
    $alice = User::factory()->onboarded()->create();
    $otherSchool = School::create([
        'code' => 'OTHER',
        'name' => 'Other',
        'short_name' => 'OTHER',
        'timezone' => 'Asia/Manila',
        'email_domains' => ['other.edu.ph'],
    ]);
    $stranger = User::factory()->create(['school_id' => $otherSchool->id]);
    $foreignSubject = Subject::create([
        'school_id' => $otherSchool->id,
        'code' => 'FOR 100',
        'name' => 'Foreign',
        'is_active' => true,
    ]);
    $resource = LearningResource::create([
        'school_id' => $otherSchool->id,
        'owner_user_id' => $stranger->id,
        'subject_id' => $foreignSubject->id,
        'type' => ResourceType::Reviewer->value,
        'title' => 'Foreign reviewer',
        'availability' => ResourceAvailability::Available->value,
        'visibility' => ResourceVisibility::School->value,
        'published_at' => now(),
    ]);

    $this->actingAs($alice)
        ->get(route('resources.show', $resource))
        ->assertNotFound();
});

it('redirects unauthenticated users away from resources', function () {
    $this->get('/resources')->assertRedirect('/login');
});
