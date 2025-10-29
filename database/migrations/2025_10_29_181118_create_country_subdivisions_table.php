<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\CountrySubdivision;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_subdivisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Country::class)->constrained();
            $table->foreignIdFor(CountrySubdivision::class)
                ->nullable()
                ->constrained();
            $table->jsonb('name');
            $table->string('code');
            $table->string('iso_code');
            $table->string('short_name');
            $table->integer('type');
            $table->jsonb('official_languages');
        });
    }
};
