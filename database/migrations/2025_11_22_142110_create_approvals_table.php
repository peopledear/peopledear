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
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(Organization::class)->constrained();
            $table->foreignIdFor(Employee::class, 'approved_by')
                ->nullable()
                ->constrained('employees');

            $table->string('approvable_type');
            $table->unsignedBigInteger('approvable_id');
            $table->string('status');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->index(['approvable_type', 'approvable_id']);
            $table->index('status');
        });
    }
};
