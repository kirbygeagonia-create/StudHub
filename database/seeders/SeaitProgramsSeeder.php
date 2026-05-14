<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\Program;
use App\Models\School;
use Illuminate\Database\Seeder;
use RuntimeException;

class SeaitProgramsSeeder extends Seeder
{
    /**
     * Seed list mirrors docs/07-seait-context.md §3.
     *
     * @var array<string, array<int, array{code: string, name: string, default_year_levels?: int}>>
     */
    public const PROGRAMS = [
        'CICT' => [
            ['code' => 'BSIT', 'name' => 'Bachelor of Science in Information Technology'],
            ['code' => 'BSIT-BAST', 'name' => 'BS Information Technology (Business Analytics)'],
            ['code' => 'ACT', 'name' => 'Associate in Computer Technology', 'default_year_levels' => 2],
        ],
        'DCE' => [
            ['code' => 'BSCE', 'name' => 'Bachelor of Science in Civil Engineering, specialized in Structural Engineering', 'default_year_levels' => 5],
        ],
        'CBGG' => [
            ['code' => 'BSBA-MM', 'name' => 'BS Business Administration major in Marketing Management'],
            ['code' => 'BSAIS', 'name' => 'BS Accounting Information Systems'],
            ['code' => 'BSHM', 'name' => 'BS Hospitality Management'],
            ['code' => 'BSTM', 'name' => 'BS Tourism Management'],
            ['code' => 'AHM', 'name' => 'Associate in Hospitality Management', 'default_year_levels' => 2],
            ['code' => 'BPA', 'name' => 'Bachelor of Public Administration'],
            ['code' => 'BSSW', 'name' => 'Bachelor of Science in Social Work'],
        ],
        'CTE' => [
            ['code' => 'BEEd', 'name' => 'Bachelor of Elementary Education'],
            ['code' => 'BECEd', 'name' => 'Bachelor of Early Childhood Education'],
            ['code' => 'BSEd-Eng', 'name' => 'Bachelor of Secondary Education major in English'],
            ['code' => 'BSEd-Math', 'name' => 'Bachelor of Secondary Education major in Mathematics'],
            ['code' => 'BSEd-SS', 'name' => 'Bachelor of Secondary Education major in Social Studies'],
            ['code' => 'BSEd-Fil', 'name' => 'Bachelor of Secondary Education major in Filipino'],
            ['code' => 'BSEd-Sci', 'name' => 'Bachelor of Secondary Education major in Science'],
            ['code' => 'BTLEd-ICT', 'name' => 'Bachelor of Technology and Livelihood Education major in ICT'],
        ],
        'CAF' => [
            ['code' => 'BSAgri-PBG', 'name' => 'BS Agriculture major in Plant Breeding and Genetics'],
            ['code' => 'BSAgri-Horti', 'name' => 'BS Agriculture major in Horticulture'],
            ['code' => 'BSAgri-AS', 'name' => 'BS Agriculture major in Animal Science'],
            ['code' => 'BSAgri-CS', 'name' => 'BS Agriculture major in Crop Science'],
            ['code' => 'BSF', 'name' => 'Bachelor of Science in Fisheries'],
            ['code' => 'BSAT', 'name' => 'Bachelor of Science in Agricultural Technology'],
        ],
        'CCJE' => [
            ['code' => 'BSCrim', 'name' => 'Bachelor of Science in Criminology'],
        ],
    ];

    public function run(): void
    {
        $school = School::where('code', 'SEAIT')->first();

        if (! $school) {
            throw new RuntimeException('SEAIT school row missing. Run SeaitSchoolSeeder first.');
        }

        foreach (self::PROGRAMS as $collegeCode => $programs) {
            $college = College::where('school_id', $school->id)
                ->where('code', $collegeCode)
                ->first();

            if (! $college) {
                throw new RuntimeException("Missing college {$collegeCode}. Run SeaitCollegesSeeder first.");
            }

            foreach ($programs as $row) {
                Program::updateOrCreate(
                    ['school_id' => $school->id, 'code' => $row['code']],
                    [
                        'college_id' => $college->id,
                        'name' => $row['name'],
                        'default_year_levels' => $row['default_year_levels'] ?? 4,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
