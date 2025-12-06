<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Organization;
use App\Models\Period;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_off_requests', function (Blueprint $table): void {
            $table->uuid('id')->primary();

            $table->timestamps();

            $table->foreignIdFor(Organization::class)
                ->constrained();

            $table->foreignIdFor(Period::class)
                ->constrained();

            $table->foreignIdFor(Employee::class)
                ->constrained();

            $table->unsignedTinyInteger('type');
            $table->unsignedTinyInteger('status');

            $table->date('start_date');
            $table->date('end_date')
                ->nullable();

            $table->boolean('is_half_day');

            $table->index(['organization_id', 'employee_id']);
            $table->index('status');
        });
    }
};
