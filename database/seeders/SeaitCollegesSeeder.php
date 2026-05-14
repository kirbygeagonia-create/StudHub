<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\School;
use Illuminate\Database\Seeder;
use RuntimeException;

class SeaitCollegesSeeder extends Seeder
{
    /**
     * @var array<int, array{code: string, name: string}>
     */
    public const COLLEGES = [
        ['code' => 'CICT', 'name' => 'College of Information and Communication Technology'],
        ['code' => 'DCE', 'name' => 'Department of Civil Engineering'],
        ['code' => 'CBGG', 'name' => 'College of Business and Good Governance'],
        ['code' => 'CTE', 'name' => 'College of Teacher Education'],
        ['code' => 'CAF', 'name' => 'College of Agriculture and Fisheries'],
        ['code' => 'CCJE', 'name' => 'College of Criminal Justice Education'],
    ];

    public function run(): void
    {
        $school = School::where('code', 'SEAIT')->first();

        if (! $school) {
            throw new RuntimeException('SEAIT school row missing. Run SeaitSchoolSeeder first.');
        }

        foreach (self::COLLEGES as $row) {
            College::updateOrCreate(
                ['school_id' => $school->id, 'code' => $row['code']],
                ['name' => $row['name']],
            );
        }
    }
}
