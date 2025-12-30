<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\TymeOffType\CreateSystemTimeOffTypes;
use App\Models\Organization;
use App\Queries\OrganizationQuery;
use Illuminate\Database\Seeder;
use Throwable;

final class TimeOffTypeSeeder extends Seeder
{
    public function __construct(
        private readonly OrganizationQuery $organizationQuery,
        private readonly CreateSystemTimeOffTypes $createSystemTimeOffTypes,
    ) {}

    public function run(): void
    {
        $organizations = $this->organizationQuery->builder()->get();

        $organizations->each(
            /**
             * @throws Throwable
             */
            function (Organization $organization): void {
                $this->createSystemTimeOffTypes->handle($organization);
            });
    }
}
