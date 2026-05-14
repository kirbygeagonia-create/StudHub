<?php

use App\Models\College;
use App\Models\Program;
use App\Models\School;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;

it('seeds a single SEAIT school row tied to Asia/Manila', function () {
    $this->seed(SeaitSchoolSeeder::class);

    expect(School::count())->toBe(1);

    $seait = School::first();
    expect($seait->code)->toBe('SEAIT');
    expect($seait->timezone)->toBe('Asia/Manila');
    expect($seait->email_domains)->toContain('seait.edu.ph');
    expect($seait->name)->toContain('South East Asian Institute');
});

it('seeds the six MVP colleges under SEAIT', function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);

    $codes = College::pluck('code')->all();

    expect($codes)->toEqualCanonicalizing(['CICT', 'DCE', 'CBGG', 'CTE', 'CAF', 'CCJE']);
});

it('seeds the full SEAIT program list across all six colleges', function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);

    expect(Program::count())->toBeGreaterThanOrEqual(20);

    $pilotCodes = ['BSIT', 'BSCE', 'BSBA-MM'];
    foreach ($pilotCodes as $code) {
        expect(Program::where('code', $code)->exists())
            ->toBeTrue("missing pilot program: {$code}");
    }

    $bsce = Program::where('code', 'BSCE')->first();
    expect($bsce->default_year_levels)->toBe(5);
    expect($bsce->college->code)->toBe('DCE');

    $bssw = Program::where('code', 'BSSW')->first();
    expect($bssw->college->code)->toBe('CBGG');
});

it('is idempotent — running seeders twice does not duplicate rows', function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);

    $schoolCount = School::count();
    $collegeCount = College::count();
    $programCount = Program::count();

    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);

    expect(School::count())->toBe($schoolCount);
    expect(College::count())->toBe($collegeCount);
    expect(Program::count())->toBe($programCount);
});
