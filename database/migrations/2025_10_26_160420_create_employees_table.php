<?php

declare(strict_types=1);

use App\Models\Office;
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
        Schema::create('employees', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Organization::class)->constrained();
            $table->foreignIdFor(Office::class)->nullable()->constrained();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('employee_number')->unique();
            $table->string('job_title')->nullable();
            $table->date('hire_date')->nullable();
            $table->integer('employment_status');
        });
    }
};
