<?php

declare(strict_types=1);

namespace App\Actions\TymeOffType;

use App\Data\PeopleDear\TimeOffType\CreateTimeOffTypeData;
use App\Enums\UserRole;
use App\Models\Organization;
use App\Queries\RoleQuery;
use Illuminate\Container\Attributes\Config;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Throwable;

final readonly class CreateSystemTimeOffTypes
{
    /**
     * @param  array<string, array<string, mixed>>  $timeOffTypes
     */
    public function __construct(
        #[Config('system_defaults.time_off_types')] private array $timeOffTypes,
        private CreateTimeOffType $createTimeOffType,
        private RoleQuery $roleQuery,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(Organization $organization): void
    {

        DB::transaction(function () use ($organization): void {

            /** @var Role $peopleManagerRole */
            $peopleManagerRole = $this->roleQuery
                ->withRole(UserRole::PeopleManager)
                ->builder()
                ->first();

            foreach ($this->timeOffTypes as $timeOffType) {
                $this->createTimeOffType->handle(
                    $organization,
                    CreateTimeOffTypeData::from([
                        ...$timeOffType,
                        'fallback_approval_role_id' => $timeOffType['requires_approval'] ? $peopleManagerRole->id : null,
                    ])
                );
            }
        });

    }
}
