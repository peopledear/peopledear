<?php

declare(strict_types=1);

use App\Enums\Support\OrganizationExcludedRoute;

it('returns string values for route checks', function (): void {
    $values = OrganizationExcludedRoute::values();

    // Basic shape
    expect($values)->toBeArray()->each->toBeString();

    $expected = [
        'org.create',
        'org.store',
        'organization-required',
        'user-profile.*',
        'password.*',
        'appearance.*',
        'two-factor.*',
        'verification.*',
        'user.destroy',
        'logout',
    ];

    expect($values)->toEqual($expected);
});
