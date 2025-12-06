<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table): void {
            $table->uuid('id')
                ->primary();
            $table->timestamps();
            $table->uuidMorphs('addressable');
            $table->string('line1');
            $table->string('line2')
                ->nullable();
            $table->string('city');
            $table->string('state')
                ->nullable();
            $table->string('postal_code');
            $table->string('country');
        });
    }
};
