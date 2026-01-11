<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table): void {
            $table->uuid('id')
                ->primary();

            $table->timestamps();

            $table->string('name');

            $table->string('identifier')
                ->unique();

            $table->string('resource_key')
                ->unique();

            $table->string('vat_number')
                ->nullable();
            $table->string('ssn')
                ->nullable();
            $table->string('phone')
                ->nullable();
        });
    }
};
