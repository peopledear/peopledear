<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Employee management permissions
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',

            // Organization management permissions
            'organizations.view',
            'organizations.create',
            'organizations.edit',
            'organizations.delete',

            // Self-management permissions
            'profile.address.edit',
            'profile.contacts.edit',
            'profile.personal.edit',

            // Team management permissions
            'teams.manage',

            // Reporting permissions
            'reports.view',

            // System settings permissions
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $employee = Role::create(['name' => 'employee']);
        $employee->givePermissionTo([
            'profile.address.edit',
            'profile.contacts.edit',
            'profile.personal.edit',
        ]);

        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo([
            'profile.address.edit',
            'profile.contacts.edit',
            'profile.personal.edit',
            'employees.view',
            'teams.manage',
            'reports.view',
        ]);

        $peopleManager = Role::create(['name' => 'people_manager']);
        $peopleManager->givePermissionTo([
            'profile.address.edit',
            'profile.contacts.edit',
            'profile.personal.edit',
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',
            'organizations.view',
            'organizations.edit',
            'teams.manage',
            'reports.view',
        ]);

        $owner = Role::create(['name' => 'owner']);
        $owner->givePermissionTo(Permission::all());
    }
};
