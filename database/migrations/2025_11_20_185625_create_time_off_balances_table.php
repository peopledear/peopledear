<?php

declare(strict_types=1);

use App\Models\Employee;
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
        Schema::create('vacation_balances', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Organization::class)
                ->constrained();
            $table->foreignIdFor(Employee::class)
                ->constrained();
            $table->smallInteger('year');
            $table->smallInteger('from_last_year');
            $table->smallInteger('accrued');
            $table->smallInteger('taken');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_off_balances');
    }
};
