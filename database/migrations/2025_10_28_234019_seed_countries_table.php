<?php

declare(strict_types=1);

use App\Actions\Country\SeedCountries;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $action = new SeedCountries;

        $action->handle();
    }
};
