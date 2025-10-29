<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $countriesJson = file_get_contents(database_path('data/countries.json'));

        throw_if($countriesJson === false, RuntimeException::class, 'Failed to read countries.json file');

        $countries = json_decode($countriesJson, true);

        throw_unless(is_array($countries), RuntimeException::class, 'Invalid JSON in countries.json file');

        $records = [];

        foreach ($countries as $country) {
            if (! is_array($country)) {
                continue;
            }

            $records[] = [
                'iso_code' => $country['isoCode'] ?? '',
                'name' => json_encode($country['name'] ?? []),
                'official_languages' => json_encode($country['officialLanguages'] ?? []),
            ];
        }

        DB::table('countries')->insert($records);
    }
};
