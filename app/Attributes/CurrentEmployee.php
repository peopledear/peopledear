<?php

declare(strict_types=1);

namespace App\Attributes;

use App\Models\Employee;
use App\Models\User;
use Attribute;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class CurrentEmployee implements ContextualAttribute
{
    /**
     * Resolve the current employee.
     *
     * @throws BindingResolutionException
     */
    public static function resolve(self $attribute, Container $container): ?Employee
    {

        /** @var User|null $user */
        $user = $container->make(Factory::class)
            ->user();

        return $user?->employee;

    }
}
