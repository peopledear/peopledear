<?php

declare(strict_types=1);

namespace Tests;

use App\Actions\Role\CreateSystemRoles;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Throwable;

use function resolve;

abstract class LandlordTestCase extends TestCase
{
    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        resolve(CreateSystemRoles::class)->handle();

    }

    final public function actingAs(Authenticatable $user, $guard = null): self
    {
        Auth::login($user, $guard);

        return $this;
    }
}
