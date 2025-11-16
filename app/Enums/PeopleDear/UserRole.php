<?php

declare(strict_types=1);

namespace App\Enums\PeopleDear;

enum UserRole: string
{
    case Owner = 'owner';
    case PeopleManager = 'people_manager';
    case Employee = 'employee';
    case Manager = 'manager';
}
