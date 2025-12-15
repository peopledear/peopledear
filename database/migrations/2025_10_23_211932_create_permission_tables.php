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
        $teams = (bool) config('permission.teams');
        /** @var array<string, string> $tableNames */
        $tableNames = (array) config('permission.table_names');
        /** @var array<string, string|null> $columnNames */
        $columnNames = (array) config('permission.column_names');
        $pivotRole = (string) ($columnNames['role_pivot_key'] ?? 'role_id');
        $pivotPermission = (string) ($columnNames['permission_pivot_key'] ?? 'permission_id');

        throw_if(empty($tableNames), Exception::class, 'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        throw_if($teams && empty($columnNames['team_foreign_key'] ?? null), Exception::class, 'Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');

        Schema::create((string) $tableNames['permissions'], static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create((string) $tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames): void {
            $table->bigIncrements('id');
            if ($teams || config('permission.testing')) {
                $table->unsignedBigInteger((string) $columnNames['team_foreign_key'])->nullable();
                $table->index((string) $columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }

            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([(string) $columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create((string) $tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams): void {
            $table->unsignedBigInteger($pivotPermission);

            $table->string('model_type');
            $table->unsignedBigInteger((string) $columnNames['model_morph_key']);
            $table->index([(string) $columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($pivotPermission)
                ->references('id')
                ->on((string) $tableNames['permissions'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger((string) $columnNames['team_foreign_key']);
                $table->index((string) $columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary([(string) $columnNames['team_foreign_key'], $pivotPermission, (string) $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            } else {
                $table->primary([$pivotPermission, (string) $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            }

        });

        Schema::create((string) $tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams): void {
            $table->unsignedBigInteger($pivotRole);

            $table->string('model_type');
            $table->unsignedBigInteger((string) $columnNames['model_morph_key']);
            $table->index([(string) $columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($pivotRole)
                ->references('id')
                ->on((string) $tableNames['roles'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger((string) $columnNames['team_foreign_key']);
                $table->index((string) $columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary([(string) $columnNames['team_foreign_key'], $pivotRole, (string) $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            } else {
                $table->primary([$pivotRole, (string) $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            }
        });

        Schema::create((string) $tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission): void {
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($pivotRole);

            $table->foreign($pivotPermission)
                ->references('id')
                ->on((string) $tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign($pivotRole)
                ->references('id')
                ->on((string) $tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        /** @var string|null $cacheStore */
        $cacheStore = config('permission.cache.store');
        /** @var string $cacheKey */
        $cacheKey = config('permission.cache.key');

        resolve(Illuminate\Contracts\Cache\Factory::class)
            ->store($cacheStore !== 'default' ? $cacheStore : null)
            ->forget($cacheKey);
    }
};
