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
}
