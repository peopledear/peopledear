<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Country\SeedCountries;
use Illuminate\Console\Command;

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
    public function handle(SeedCountries $seedCountriesAction): int
    {
        info('Installing PeopleDear...');

        spin(
            callback: $seedCountriesAction->handle(...),
            message: 'Seeding countries...',
        );

        info('Installation complete!');

        return self::SUCCESS;
    }
}
