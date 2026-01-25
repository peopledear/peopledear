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

        /** @var array<string, string> $tableNames */
        $tableNames = (array) config('permission.table_names');
        /** @var array<string, string|null> $columnNames */
        $columnNames = (array) config('permission.column_names');

        $modelKey = (string) ($columnNames['model_morph_key'] ?? 'model_id');
        $pivotRole = (string) ($columnNames['role_pivot_key'] ?? 'role_id');
        $pivotPermission = (string) ($columnNames['permission_pivot_key'] ?? 'permission_id');

        $this->convertMorphKeyToString(
            table: (string) $tableNames['model_has_permissions'],
            pivotColumn: $pivotPermission,
            referenceTable: (string) $tableNames['permissions'],
            modelKey: $modelKey,
            primaryName: 'model_has_permissions_permission_model_type_primary',
            indexName: 'model_has_permissions_model_id_model_type_index',
        );

        $this->convertMorphKeyToString(
            table: (string) $tableNames['model_has_roles'],
            pivotColumn: $pivotRole,
            referenceTable: (string) $tableNames['roles'],
            modelKey: $modelKey,
            primaryName: 'model_has_roles_role_model_type_primary',
            indexName: 'model_has_roles_model_id_model_type_index',
        );
    }

    private function convertMorphKeyToString(
        string $table,
        string $pivotColumn,
        string $referenceTable,
        string $modelKey,
        string $primaryName,
        string $indexName,
    ): void {
        Schema::table($table, static function (Blueprint $table) use ($pivotColumn, $primaryName, $indexName): void {
            $table->dropForeign([$pivotColumn]);
            $table->dropPrimary($primaryName);
            $table->dropIndex($indexName);
        });

        DB::statement(sprintf(
            'ALTER TABLE "%s" ALTER COLUMN "%s" TYPE varchar(36) USING "%s"::text',
            $table,
            $modelKey,
            $modelKey,
        ));

        Schema::table($table, static function (Blueprint $table) use ($pivotColumn, $referenceTable, $modelKey, $primaryName, $indexName): void {
            $table->index([$modelKey, 'model_type'], $indexName);
            $table->primary([$pivotColumn, $modelKey, 'model_type'], $primaryName);
            $table->foreign($pivotColumn)
                ->references('id')
                ->on($referenceTable)
                ->onDelete('cascade');
        });
    }

    private function isPostgresConnection(): bool
    {
        return Schema::getConnection()->getDriverName() === 'pgsql';
    }
};
