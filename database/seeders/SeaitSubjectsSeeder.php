<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\School;
use App\Models\Subject;
use App\Models\SubjectAlias;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Seeds the SEAIT subject graph for the Week-11 pilot trio
 * (BSIT × BSCE × BSBA-MM): GE core subjects shared across all programs,
 * plus program-specific technical subjects, plus a starter set of
 * subject_aliases for student shorthand. Documented in
 * docs/03-database-schema.md §4 and docs/07-seait-context.md §5.
 *
 * Idempotent — re-running updates existing rows by `(school_id, code)`.
 */
class SeaitSubjectsSeeder extends Seeder
{
    /**
     * @var array<int, array{code: string, name: string, domain: string, description?: string, aliases?: array<int, string>, programs?: array<string, array{year?: int, weight?: float}>}>
     */
    public const SUBJECTS = [
        // ---------- General Education core (shared across all programs) ----------
        [
            'code' => 'GE 114',
            'name' => 'Mathematics in the Modern World',
            'domain' => 'Math',
            'aliases' => ['MMW', 'Math in the Modern World', 'Modern Math'],
            'programs' => [
                'BSIT' => ['year' => 1, 'weight' => 1.0],
                'BSCE' => ['year' => 1, 'weight' => 1.0],
                'BSBA-MM' => ['year' => 1, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'GE 115',
            'name' => 'Purposive Communication',
            'domain' => 'Communication',
            'aliases' => ['PurCom', 'Purposive Comm'],
            'programs' => [
                'BSIT' => ['year' => 1, 'weight' => 1.0],
                'BSCE' => ['year' => 1, 'weight' => 1.0],
                'BSBA-MM' => ['year' => 1, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'GE 116',
            'name' => 'Understanding the Self',
            'domain' => 'Social Science',
            'aliases' => ['UTS'],
            'programs' => [
                'BSIT' => ['year' => 1, 'weight' => 1.0],
                'BSCE' => ['year' => 1, 'weight' => 1.0],
                'BSBA-MM' => ['year' => 1, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'GE 117',
            'name' => 'Readings in Philippine History',
            'domain' => 'History',
            'aliases' => ['RPH', 'Phil History'],
            'programs' => [
                'BSIT' => ['year' => 1, 'weight' => 1.0],
                'BSCE' => ['year' => 1, 'weight' => 1.0],
                'BSBA-MM' => ['year' => 1, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'GE 121',
            'name' => 'Ethics',
            'domain' => 'Philosophy',
            'aliases' => ['Ethics', 'Ethics in PH'],
            'programs' => [
                'BSIT' => ['year' => 2, 'weight' => 1.0],
                'BSCE' => ['year' => 2, 'weight' => 1.0],
                'BSBA-MM' => ['year' => 2, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'NSTP 111',
            'name' => 'NSTP: Civic Welfare Training Service 1',
            'domain' => 'NSTP',
            'aliases' => ['NSTP 1', 'CWTS 1'],
            'programs' => [
                'BSIT' => ['year' => 1, 'weight' => 1.0],
                'BSCE' => ['year' => 1, 'weight' => 1.0],
                'BSBA-MM' => ['year' => 1, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'PE 111',
            'name' => 'Physical Activities Toward Health and Fitness 1',
            'domain' => 'PE',
            'aliases' => ['PATHFit 1', 'PE 1'],
            'programs' => [
                'BSIT' => ['year' => 1, 'weight' => 1.0],
                'BSCE' => ['year' => 1, 'weight' => 1.0],
                'BSBA-MM' => ['year' => 1, 'weight' => 1.0],
            ],
        ],

        // ---------- BSIT technical core ----------
        [
            'code' => 'ITCC 111',
            'name' => 'Introduction to Computing',
            'domain' => 'Computing',
            'aliases' => ['Intro to Computing', 'IT Fundamentals'],
            'programs' => [
                'BSIT' => ['year' => 1, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'IT 121',
            'name' => 'Computer Programming 1',
            'domain' => 'Computing',
            'aliases' => ['CP1', 'Programming 1', 'C++ Programming'],
            'programs' => [
                'BSIT' => ['year' => 1, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'IT 122',
            'name' => 'Computer Programming 2',
            'domain' => 'Computing',
            'aliases' => ['CP2', 'Programming 2'],
            'programs' => [
                'BSIT' => ['year' => 1, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'IT 211',
            'name' => 'Data Structures and Algorithms',
            'domain' => 'Computing',
            'aliases' => ['DSA', 'Algorithms', 'Data Structures'],
            'programs' => [
                'BSIT' => ['year' => 2, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'IT 212',
            'name' => 'Database Management Systems',
            'domain' => 'Computing',
            'aliases' => ['DBMS', 'Databases', 'SQL'],
            'programs' => [
                'BSIT' => ['year' => 2, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'IT 311',
            'name' => 'Web Development',
            'domain' => 'Computing',
            'aliases' => ['Web Dev', 'Webdev'],
            'programs' => [
                'BSIT' => ['year' => 3, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'IT 321',
            'name' => 'Networking 1',
            'domain' => 'Computing',
            'aliases' => ['Networking', 'Computer Networks'],
            'programs' => [
                'BSIT' => ['year' => 3, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'IT 411',
            'name' => 'Capstone Project 1',
            'domain' => 'Computing',
            'aliases' => ['Capstone 1', 'Thesis 1'],
            'programs' => [
                'BSIT' => ['year' => 4, 'weight' => 1.0],
            ],
        ],

        // ---------- BSCE technical core ----------
        [
            'code' => 'CE 111',
            'name' => 'Engineering Drawing',
            'domain' => 'Engineering',
            'aliases' => ['Engg Drawing', 'EngDraw'],
            'programs' => [
                'BSCE' => ['year' => 1, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'CE 121',
            'name' => 'Statics of Rigid Bodies',
            'domain' => 'Engineering',
            'aliases' => ['Statics'],
            'programs' => [
                'BSCE' => ['year' => 2, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'CE 122',
            'name' => 'Dynamics of Rigid Bodies',
            'domain' => 'Engineering',
            'aliases' => ['Dynamics'],
            'programs' => [
                'BSCE' => ['year' => 2, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'CE 211',
            'name' => 'Mechanics of Deformable Bodies',
            'domain' => 'Engineering',
            'aliases' => ['MoDB', 'Mech of Materials'],
            'programs' => [
                'BSCE' => ['year' => 2, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'CE 311',
            'name' => 'Structural Analysis',
            'domain' => 'Engineering',
            'aliases' => ['Structures', 'Struct Analysis'],
            'programs' => [
                'BSCE' => ['year' => 3, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'CE 312',
            'name' => 'Reinforced Concrete Design',
            'domain' => 'Engineering',
            'aliases' => ['RCD', 'Reinforced Concrete'],
            'programs' => [
                'BSCE' => ['year' => 3, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'CE 411',
            'name' => 'Civil Engineering Project',
            'domain' => 'Engineering',
            'aliases' => ['CE Project'],
            'programs' => [
                'BSCE' => ['year' => 5, 'weight' => 1.0],
            ],
        ],

        // ---------- Math (shared between BSIT & BSCE) ----------
        [
            'code' => 'MATH 121',
            'name' => 'Calculus 1',
            'domain' => 'Math',
            'aliases' => ['Calc 1', 'Calculus I'],
            'programs' => [
                'BSCE' => ['year' => 1, 'weight' => 1.0],
                'BSIT' => ['year' => 1, 'weight' => 0.8],
            ],
        ],
        [
            'code' => 'MATH 122',
            'name' => 'Calculus 2',
            'domain' => 'Math',
            'aliases' => ['Calc 2', 'Calculus II'],
            'programs' => [
                'BSCE' => ['year' => 1, 'weight' => 1.0],
                'BSIT' => ['year' => 2, 'weight' => 0.6],
            ],
        ],

        // ---------- BSBA-MM technical core ----------
        [
            'code' => 'BA 111',
            'name' => 'Principles of Management',
            'domain' => 'Business',
            'aliases' => ['POM', 'Management'],
            'programs' => [
                'BSBA-MM' => ['year' => 1, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'BA 121',
            'name' => 'Principles of Marketing',
            'domain' => 'Business',
            'aliases' => ['POMK', 'Marketing 101'],
            'programs' => [
                'BSBA-MM' => ['year' => 1, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'BA 211',
            'name' => 'Microeconomics',
            'domain' => 'Economics',
            'aliases' => ['Micro Econ', 'Microecon'],
            'programs' => [
                'BSBA-MM' => ['year' => 2, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'BA 212',
            'name' => 'Macroeconomics',
            'domain' => 'Economics',
            'aliases' => ['Macro Econ', 'Macroecon'],
            'programs' => [
                'BSBA-MM' => ['year' => 2, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'BA 311',
            'name' => 'Marketing Research',
            'domain' => 'Business',
            'aliases' => ['Mkt Research'],
            'programs' => [
                'BSBA-MM' => ['year' => 3, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'BA 312',
            'name' => 'Consumer Behavior',
            'domain' => 'Business',
            'aliases' => ['Consumer Beh'],
            'programs' => [
                'BSBA-MM' => ['year' => 3, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'BA 411',
            'name' => 'Strategic Marketing',
            'domain' => 'Business',
            'aliases' => ['Strat Mktg'],
            'programs' => [
                'BSBA-MM' => ['year' => 4, 'weight' => 1.0],
            ],
        ],
        [
            'code' => 'ACC 111',
            'name' => 'Financial Accounting',
            'domain' => 'Business',
            'aliases' => ['Financial Acc', 'FinAcc'],
            'programs' => [
                'BSBA-MM' => ['year' => 1, 'weight' => 1.0],
            ],
        ],
    ];

    public function run(): void
    {
        $school = School::where('code', 'SEAIT')->first();

        if (! $school) {
            throw new RuntimeException('SEAIT school row missing. Run SeaitSchoolSeeder first.');
        }

        $programIds = Program::where('school_id', $school->id)
            ->whereIn('code', ['BSIT', 'BSCE', 'BSBA-MM'])
            ->pluck('id', 'code');

        foreach (self::SUBJECTS as $row) {
            /** @var Subject $subject */
            $subject = Subject::updateOrCreate(
                ['school_id' => $school->id, 'code' => $row['code']],
                [
                    'name' => $row['name'],
                    'domain' => $row['domain'] ?? null,
                    'description' => $row['description'] ?? null,
                    'is_active' => true,
                ],
            );

            foreach ($row['aliases'] ?? [] as $alias) {
                SubjectAlias::updateOrCreate(
                    ['subject_id' => $subject->id, 'alias' => $alias],
                    [],
                );
            }

            foreach ($row['programs'] ?? [] as $programCode => $meta) {
                if (! isset($programIds[$programCode])) {
                    continue;
                }

                $subject->programs()->syncWithoutDetaching([
                    $programIds[$programCode] => [
                        'typical_year_level' => $meta['year'] ?? null,
                        'weight' => $meta['weight'] ?? 1.0,
                    ],
                ]);
            }
        }
    }
}
