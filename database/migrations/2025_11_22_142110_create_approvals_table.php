<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table): void {
            $table->uuid('id')->primary();

            $table->timestamps();

            $table->foreignIdFor(Organization::class)
                ->constrained();

            $table->foreignIdFor(Employee::class, 'approved_by')
                ->nullable()
                ->constrained('employees');

            $table->uuidMorphs('approvable');

            $table->string('status');
            $table->text('rejection_reason')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->index('status');
        });
    }
};
