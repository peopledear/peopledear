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
        $driver = Schema::getConnection()->getDriverName();
        $indexName = 'sessions_user_id_index';

        if ($driver !== 'pgsql') {
            Schema::table('sessions', static function (Blueprint $table) use ($indexName): void {
                $table->string('user_id', 36)->nullable()->change();
                $table->dropIndex($indexName);
                $table->index('user_id', $indexName);
            });

            return;
        }

        DB::statement('DROP INDEX IF EXISTS "sessions_user_id_index"');
        DB::statement('ALTER TABLE "sessions" DROP CONSTRAINT IF EXISTS "sessions_user_id_foreign"');
        DB::statement(
            'ALTER TABLE "sessions" ALTER COLUMN "user_id" TYPE varchar(36) USING "user_id"::text'
        );

        Schema::table('sessions', static function (Blueprint $table) use ($indexName): void {
            $table->index('user_id', $indexName);
        });
    }
};
