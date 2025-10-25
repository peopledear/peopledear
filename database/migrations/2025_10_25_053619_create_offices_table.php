<?php

declare(strict_types=1);

use App\Enums\OfficeType;
use App\Models\Organization;
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
        Schema::create('offices', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Organization::class)->constrained();
            $table->string('name');
            $table->integer('type')->default(OfficeType::Branch->value);
            $table->string('phone')->nullable();
        });
    }
};
