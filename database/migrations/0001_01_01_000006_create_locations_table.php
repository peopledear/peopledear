<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(new Location()->getTable(), function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->timestamps();

            $table->foreignIdFor(Organization::class)->constrained();
            $table->foreignIdFor(Country::class)->constrained();

            $table->string('name');
            $table->integer('type');
            $table->string('phone')->nullable();

            $table->unique(['organization_id', 'country_id', 'type']);
        });
    }
};
