<?php

declare(strict_types=1);

use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('time_off_types', function (Blueprint $table): void {
            $table->uuid('id')
                ->primary();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignIdFor(Organization::class)
                ->constrained();

            $table->foreignIdFor(Role::class, 'fallback_approval_role_id')
                ->nullable()
                ->constrained('roles');

            $table->string('name');
            $table->text('description')
                ->nullable();
            $table->boolean('is_system');
            $table->jsonb('allowed_units');
            $table->string('icon');
            $table->string('color');
            $table->boolean('is_active');
            $table->boolean('requires_approval');
            $table->boolean('requires_justification');
            $table->boolean('requires_justification_document');
            $table->unsignedSmallInteger('balance_mode');

        });
    }
};
