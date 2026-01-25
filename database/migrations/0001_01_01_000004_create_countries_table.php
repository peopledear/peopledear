<?php

declare(strict_types=1);

use App\Models\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(new Country()->getTable(), function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('iso_code')->unique();
            $table->json('name');
            $table->json('official_languages');
        });
    }
};
