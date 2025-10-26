<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table): void {
            $table->string('country_iso_code')->nullable();
            $table->string('subdivision_code')->nullable();
            $table->string('language_iso_code')->nullable();
        });
    }
};
