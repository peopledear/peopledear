<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
            $table->string('name')
                ->unique();
            $table->string('display_name');
            $table->text('description')
                ->nullable();
        });

        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access with all permissions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Can manage team members and approve requests',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'Standard employee access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};
