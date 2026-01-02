<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Queries\OrganizationQuery;
use Illuminate\Support\Str;

final readonly class MakeOrganizationIdentifier
{
    public function __construct(
        private OrganizationQuery $organizationQuery,
    ) {}

    public function handle(string $name): string
    {

        $exists = $this->organizationQuery
            ->identifier(Str::slug($name))
            ->exists();

        if ($exists) {
            $uniqueSuffix = Str::lower(Str::random(3));

            return $this->handle($name.' '.$uniqueSuffix);
        }

        return Str::slug($name);
    }
}
