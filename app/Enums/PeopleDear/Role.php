<?php

declare(strict_types=1);

namespace App\Enums\PeopleDear;

enum Role: string
{
    case Employee = 'employee';
    case Manager = 'manager';
    case PeopleManager = 'people_manager';
    case Owner = 'owner';
    case Administrator = 'administrator';

}
