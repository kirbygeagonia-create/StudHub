<?php

use App\Domain\Catalog\Actions\SearchResources;
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

function makeResource(User $owner, string $title, array $overrides = []): LearningResource
{
    /** @var Subject $defaultSubject */
    $defaultSubject = Subject::where('code', 'IT 211')->firstOrFail();

    return LearningResource::create(array_merge([
        'school_id' => $owner->school_id,
        'owner_user_id' => $owner->id,
        'subject_id' => $defaultSubject->id,
        'program_id' => $owner->program_id,
        'type' => ResourceType::Reviewer->value,
        'title' => $title,
        'description' => null,
        'availability' => ResourceAvailability::Available->value,
        'visibility' => ResourceVisibility::School->value,
        'published_at' => now(),
    ], $overrides));
}

it('lists only resources from the viewer\'s school', function () {
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
    LearningResource::create([
        'school_id' => $otherSchool->id,
        'owner_user_id' => $stranger->id,
        'subject_id' => $foreignSubject->id,
        'type' => ResourceType::Reviewer->value,
        'title' => 'From another school',
        'availability' => ResourceAvailability::Available->value,
        'visibility' => ResourceVisibility::School->value,
        'published_at' => now(),
    ]);
    makeResource($alice, 'Local DSA reviewer');

    $results = (new SearchResources)->handle($alice);

    expect($results->total())->toBe(1);
    expect($results->first()->title)->toBe('Local DSA reviewer');
});

it('hides program_only resources from outside the program', function () {
    $alice = User::factory()->onboarded()->create();
    $bsce = Program::where('code', 'BSCE')->firstOrFail();
    $bob = User::factory()->onboarded()->create([
        'school_id' => $alice->school_id,
        'program_id' => $bsce->id,
        'college_id' => $bsce->college_id,
    ]);
    /** @var Subject $subject */
    $subject = Subject::where('code', 'IT 211')->firstOrFail();
    makeResource($bob, 'BSCE-only reviewer', [
        'subject_id' => $subject->id,
        'visibility' => ResourceVisibility::ProgramOnly->value,
    ]);

    $aliceResults = (new SearchResources)->handle($alice);

    expect($aliceResults->total())->toBe(0);

    $bobResults = (new SearchResources)->handle($bob);
    expect($bobResults->total())->toBe(1);
});

it('filters by subject, type, program, and year', function () {
    $alice = User::factory()->onboarded()->create();
    $dsa = Subject::where('code', 'IT 211')->firstOrFail();
    $calc2 = Subject::where('code', 'MATH 122')->firstOrFail();

    makeResource($alice, 'DSA notes', ['subject_id' => $dsa->id, 'type' => ResourceType::LectureNotes->value, 'year_level' => 2]);
    makeResource($alice, 'DSA reviewer', ['subject_id' => $dsa->id, 'type' => ResourceType::Reviewer->value, 'year_level' => 2]);
    makeResource($alice, 'Calc 2 past exam', ['subject_id' => $calc2->id, 'type' => ResourceType::PastExam->value, 'year_level' => 1]);

    $search = new SearchResources;

    expect($search->handle($alice, ['subject_id' => $dsa->id])->total())->toBe(2);
    expect($search->handle($alice, ['type' => ResourceType::Reviewer->value])->total())->toBe(1);
    expect($search->handle($alice, ['year_level' => 1])->total())->toBe(1);
    expect($search->handle($alice, ['program_id' => $alice->program_id])->total())->toBe(3);
});

it('searches across title and description (LIKE on sqlite)', function () {
    $alice = User::factory()->onboarded()->create();
    makeResource($alice, 'DSA midterm reviewer', ['description' => 'covers sorting + binary trees']);
    makeResource($alice, 'Networking review', ['description' => 'subnets + routing']);

    $search = new SearchResources;

    $hits = $search->handle($alice, ['q' => 'trees']);
    expect($hits->total())->toBe(1);
    expect($hits->first()->title)->toBe('DSA midterm reviewer');

    $hits = $search->handle($alice, ['q' => 'review']);
    expect($hits->total())->toBe(2);
});

it('excludes archived and soft-deleted resources from search', function () {
    $alice = User::factory()->onboarded()->create();

    makeResource($alice, 'Live one');
    $archived = makeResource($alice, 'Archived one', ['availability' => ResourceAvailability::Archived->value]);
    $deleted = makeResource($alice, 'Deleted one');
    $deleted->delete();

    $results = (new SearchResources)->handle($alice);

    expect($results->total())->toBe(1);
    expect($results->first()->title)->toBe('Live one');
});
