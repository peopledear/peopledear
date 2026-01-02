<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use Illuminate\Support\Str;

final class MakeOrganizationResourceKey
{
    public function handle(): string
    {
        return Str::ulid()->toString();
    }
}
