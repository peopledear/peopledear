<?php

declare(strict_types=1);

use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table): void {
            $table->id();
            $table->timestamp('synced_at');
            $table->foreignIdFor(Organization::class)->constrained();
            $table->unsignedTinyInteger('job_type');
            $table->unsignedTinyInteger('status');
            $table->integer('records_synced_count');
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();

            $table->index(['organization_id', 'synced_at']);
            $table->index('job_type');
        });
    }
};
