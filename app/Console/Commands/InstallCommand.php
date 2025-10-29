<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Country\InsertCountries;
use App\Data\PeopleDear\Country\InsertCountryData;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

final class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the application by seeding required data';

    /**
     * Execute the console command.
     */
    public function handle(InsertCountries $seedCountriesAction): int
    {
        info('Installing PeopleDear...');

        spin(
            callback: function () use ($seedCountriesAction): void {
                $contents = file_get_contents(database_path('data/countries.json'));
                assert(is_string($contents));
                /** @var array<int, array<string, mixed>> $data */
                $data = json_decode($contents, true);
                /** @var Collection<int, array<string, mixed>> $countries */
                $countries = collect($data);
                /** @var Collection<int, InsertCountryData> $collectionOfInsertCountry */
                $collectionOfInsertCountry = $countries->map(fn (mixed $country): InsertCountryData => InsertCountryData::from($country));

                $seedCountriesAction->handle($collectionOfInsertCountry);
            },
            message: 'Seeding countries...',
        );

        info('Installation complete!');

        return self::SUCCESS;
    }
}
