<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Organization;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TenantTestCase extends BaseTestCase
{
    protected Organization $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Organization::factory()
            ->createQuietly([
                'name' => 'Acme Corporation',
                'identifier' => 'acme',
            ]);

    }
}
