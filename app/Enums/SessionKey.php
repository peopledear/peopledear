<?php

declare(strict_types=1);

namespace App\Enums;

enum SessionKey: string
{
    case CurrentOrganization = 'current_organization';
}
