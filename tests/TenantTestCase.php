<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Organization;

abstract class TenantTestCase extends WithUsersTestCase
{
    protected Organization $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = $this->organization;

    }
}
