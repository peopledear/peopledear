<?php

declare(strict_types=1);

namespace App\Attributes;

use App\Enums\Support\SessionKey;
use App\Models\Organization;
use Attribute;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;
use Illuminate\Support\Facades\Session;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class CurrentOrganization implements ContextualAttribute
{
    /**
     * Resolve the current organization.
     */
    public static function resolve(self $attribute, Container $container): ?Organization
    {
        $organizationId = Session::get(SessionKey::CurrentOrganization->value);

        if (!$organizationId) {
            return null;
        }

        /** @var Organization|null */
        return Organization::query()->find($organizationId);
    }
}
