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
        Schema::create('organizations', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('vat_number')->nullable();
            $table->string('ssn')->nullable();
            $table->string('phone')->nullable();
            $table->foreignIdFor(Country::class)
                ->nullable()
                ->constrained();
        });
    }
};
