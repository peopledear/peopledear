<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\Holiday;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(new Holiday()->getTable(), function (Blueprint $table): void {
            $table->uuid('id')
                ->primary();
            $table->timestamps();

            $table->foreignIdFor(Organization::class)
                ->constrained();

            $table->foreignIdFor(Country::class)
                ->constrained();

            $table->date('date');
            $table->json('name');
            $table->unsignedTinyInteger('type');
            $table->boolean('nationwide');
            $table->string('subdivision_code')->nullable();
            $table->string('api_holiday_id')->nullable();
            $table->boolean('is_custom');

            $table->index(['organization_id', 'date']);
            $table->index(['country_id', 'subdivision_code', 'date']);
            $table->unique(['organization_id', 'api_holiday_id'], 'unique_org_api_holiday');
        });
    }
};
