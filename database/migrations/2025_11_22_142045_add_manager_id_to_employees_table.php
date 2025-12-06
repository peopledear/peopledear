<?php

declare(strict_types=1);

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $table->foreignIdFor(Employee::class, 'manager_id')
                ->nullable()
                ->constrained('employees');
        });
    }
};
