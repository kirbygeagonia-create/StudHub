<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SeaitSchoolSeeder extends Seeder
{
    public function run(): void
    {
        School::updateOrCreate(
            ['code' => 'SEAIT'],
            [
                'name' => 'South East Asian Institute of Technology, Inc.',
                'short_name' => 'SEAIT',
                'timezone' => 'Asia/Manila',
                'email_domains' => self::allowedEmailDomains(),
                'location' => 'Tupi, South Cotabato, Philippines',
            ],
        );
    }

    /**
     * @return array<int, string>
     */
    public static function allowedEmailDomains(): array
    {
        $raw = (string) config('studhub.allowed_email_domains', 'seait.edu.ph,students.seait.edu.ph');

        $domains = array_filter(array_map(
            fn (string $d) => strtolower(trim($d)),
            explode(',', $raw),
        ));

        return array_values(array_unique($domains));
    }
}
