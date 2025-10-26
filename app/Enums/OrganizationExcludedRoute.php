<?php

declare(strict_types=1);

namespace App\Enums;

enum OrganizationExcludedRoute: string
{
    case OrgCreate = 'org.create';
    case OrgStore = 'org.store';
    case OrganizationRequired = 'organization-required';
    case UserProfile = 'user-profile.*';
    case Password = 'password.*';
    case Appearance = 'appearance.*';
    case TwoFactor = 'two-factor.*';
    case Verification = 'verification.*';
    case UserDestroy = 'user.destroy';
    case Logout = 'logout';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(fn (self $r) => $r->value, self::cases());
    }
}
