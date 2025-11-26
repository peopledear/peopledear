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
        if (Schema::hasColumn('notifications', 'organization_id')) {
            Schema::table('notifications', function (Blueprint $table): void {
                $table->dropIndex(['organization_id']);
            });

            Schema::table('notifications', function (Blueprint $table): void {
                $table->dropColumn('organization_id');
            });
        }
    }
};
