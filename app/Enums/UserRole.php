<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Employee = 'employee';
    case Manager = 'manager';
    case Owner = 'owner';
    case PeopleManager = 'people_manager';

    public function description(): string
    {
        return match ($this) {
            self::Employee => 'Employee with standard access',
            self::Manager => 'Manages team members, approves time-offs and overtime requests',
            self::Owner => 'Full access to all organization settings, employees, time-offs, and reports',
            self::PeopleManager => 'People Manager with specific HR access',
        };
    }

    /**
     * @return array<UserPermission>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Employee, self::Manager => [
                UserPermission::ProfileEdit,
                UserPermission::TimeOffRequestCreate,
                UserPermission::TimeOffRequestView,
                UserPermission::TimeOffRequestEdit,
                UserPermission::TimeOffRequestDelete,
            ],
            self::Owner => [
                ...self::PeopleManager->permissions(),
                UserPermission::OrganizationCreate,
                UserPermission::OrganizationDelete,
            ],
            self::PeopleManager => [
                UserPermission::TimeOffTypeManage,
                UserPermission::TimeOffTypeView,
                UserPermission::TimeOffTypeCreate,
                UserPermission::TimeOffTypeEdit,
                UserPermission::TimeOffTypeDelete,
                UserPermission::TimeOffTypeActivate,
                UserPermission::TimeOffTypeDeactivate,
                UserPermission::TimeOffRequestManage,
                UserPermission::LocationManage,
                UserPermission::LocationCreate,
                UserPermission::LocationEdit,
                UserPermission::LocationsDelete,
                UserPermission::OrganizationView,
                UserPermission::OrganizationEdit,
            ],
        };
    }
}
