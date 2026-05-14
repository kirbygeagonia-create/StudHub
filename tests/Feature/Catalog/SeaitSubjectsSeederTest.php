<?php

use App\Models\Program;
use App\Models\Subject;
use App\Models\SubjectAlias;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;
use Database\Seeders\SeaitSubjectsSeeder;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
});

it('seeds the GE core subjects shared across the pilot trio', function () {
    $this->seed(SeaitSubjectsSeeder::class);

    expect(Subject::where('code', 'GE 114')->exists())->toBeTrue();
    expect(Subject::where('code', 'GE 115')->exists())->toBeTrue();
    expect(Subject::where('code', 'NSTP 111')->exists())->toBeTrue();

    /** @var Subject $mmw */
    $mmw = Subject::where('code', 'GE 114')->firstOrFail();
    $programCodes = $mmw->programs()->pluck('code')->all();

    expect($programCodes)->toContain('BSIT');
    expect($programCodes)->toContain('BSCE');
    expect($programCodes)->toContain('BSBA-MM');
});

it('seeds at least one subject for each pilot program', function () {
    $this->seed(SeaitSubjectsSeeder::class);

    foreach (['BSIT', 'BSCE', 'BSBA-MM'] as $code) {
        /** @var Program $program */
        $program = Program::where('code', $code)->firstOrFail();
        expect($program->subjects()->count())
            ->toBeGreaterThan(0, "expected pilot program {$code} to have at least one subject");
    }
});

it('persists subject aliases for student shorthand', function () {
    $this->seed(SeaitSubjectsSeeder::class);

    /** @var Subject $dsa */
    $dsa = Subject::where('code', 'IT 211')->firstOrFail();
    $aliases = $dsa->aliases()->pluck('alias')->all();

    expect($aliases)->toContain('DSA');
    expect($aliases)->toContain('Algorithms');
});

it('is idempotent — running the subjects seeder twice produces stable counts', function () {
    $this->seed(SeaitSubjectsSeeder::class);

    $subjectCount = Subject::count();
    $aliasCount = SubjectAlias::count();
    $pivotCount = DB::table('program_subjects')->count();

    $this->seed(SeaitSubjectsSeeder::class);

    expect(Subject::count())->toBe($subjectCount);
    expect(SubjectAlias::count())->toBe($aliasCount);
    expect(DB::table('program_subjects')->count())->toBe($pivotCount);
});

it('records typical year level and weight on program_subjects rows', function () {
    $this->seed(SeaitSubjectsSeeder::class);

    /** @var Subject $calc2 */
    $calc2 = Subject::where('code', 'MATH 122')->firstOrFail();
    /** @var Program $bsce */
    $bsce = Program::where('code', 'BSCE')->firstOrFail();

    $pivot = $calc2->programs()->where('program_id', $bsce->id)->first()?->pivot;

    expect($pivot)->not->toBeNull();
    expect((int) $pivot->typical_year_level)->toBe(1);
    expect((float) $pivot->weight)->toEqualWithDelta(1.0, 0.001);
});
