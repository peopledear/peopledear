<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->isPostgresConnection()) {
            return;
        }

        $indexName = 'notifications_notifiable_type_notifiable_id_index';

        Schema::table('notifications', static function (Blueprint $table) use ($indexName): void {
            $table->dropIndex($indexName);
        });

        DB::statement(
            'ALTER TABLE "notifications" ALTER COLUMN "notifiable_id" TYPE varchar(36) USING "notifiable_id"::text'
        );

        Schema::table('notifications', static function (Blueprint $table) use ($indexName): void {
            $table->index(['notifiable_type', 'notifiable_id'], $indexName);
        });
    }

    private function isPostgresConnection(): bool
    {
        return Schema::getConnection()->getDriverName() === 'pgsql';
    }
};
