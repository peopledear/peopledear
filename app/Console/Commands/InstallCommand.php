<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Country\UpsertCountries;
use App\Actions\CountrySubdivision\CreateRootCountrySubdivision;
use App\Data\Integrations\OpenHolidays\OpenHolidaysSubdivisionData;
use App\Data\PeopleDear\Country\InsertCountryData;
use App\Http\Integrations\OpenHolidays\Adapters\OpenHolidaysSubdivisionAdapter;
use App\Http\Integrations\OpenHolidays\OpenHolidaysConnector;
use App\Http\Integrations\OpenHolidays\Requests\GetSubdivisions;
use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

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
    public function handle(
        UpsertCountries $seedCountriesAction,
        CreateRootCountrySubdivision $createRootCountrySubdivision,
        OpenHolidaysSubdivisionAdapter $subdivisionAdapter,
        OpenHolidaysConnector $connector
    ): int {
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

        spin(
            callback: function () use ($createRootCountrySubdivision, $subdivisionAdapter, $connector): void {
                $countryCodes = ['PT', 'ES'];
                $rateLimitDelayMs = config('openholidays.rate_limit.delay_ms', 500);

                foreach ($countryCodes as $index => $countryIsoCode) {
                    try {
                        /** @var Country|null $country */
                        $country = Country::query()
                            ->where('iso_code', $countryIsoCode)
                            ->first();

                        if ($country === null) {
                            Log::warning('Country not found for ISO code: '.$countryIsoCode);

                            continue;
                        }

                        $request = new GetSubdivisions($countryIsoCode);
                        $response = $connector->send($request);

                        /** @var array<int, array<string, mixed>> $subdivisions */
                        $subdivisions = $response->json();

                        if ($subdivisions === [] || $subdivisions === null) {
                            Log::info('No subdivisions found for country: '.$countryIsoCode);

                            continue;
                        }

                        foreach ($subdivisions as $subdivisionData) {
                            $subdivisionDto = OpenHolidaysSubdivisionData::from($subdivisionData);

                            $createData = $subdivisionAdapter->toCreateData(
                                $subdivisionDto,
                                countryId: $country->id,
                                countryLanguages: $country->official_languages
                            );

                            $createRootCountrySubdivision->handle($createData);
                        }

                        if ($index < count($countryCodes) - 1) {
                            \Illuminate\Support\Sleep::usleep($rateLimitDelayMs * 1000);
                        }
                    } catch (Throwable $e) {
                        Log::error('Failed to fetch subdivisions for '.$countryIsoCode, [
                            'country_iso_code' => $countryIsoCode,
                            'error' => $e->getMessage(),
                            'exception_class' => $e::class,
                        ]);

                        continue;
                    }
                }
            },
            message: 'Fetching country subdivisions...',
        );

        info('Installation complete!');

        return self::SUCCESS;
    }
}
