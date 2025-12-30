<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Employee = 'employee';
    case Manager = 'manager';
    case Owner = 'owner';
    case PeopleManager = 'people_manager';
}
