<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Actions\TymeOffType\CreateSystemTimeOffTypes;
use App\Actions\User\CreateUser;
use App\Data\PeopleDear\CreateRegistrationData;
use App\Data\PeopleDear\CreateUserData;
use App\Data\PeopleDear\Organization\CreateOrganizationData;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class RegisterOrganization
{
    public function __construct(
        private CreateOrganization $createOrganization,
        private CreateSystemTimeOffTypes $createSystemTimeOffTypes,
        private CreateUser $createUser,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(CreateRegistrationData $data): User
    {
        return DB::transaction(function () use ($data): User {

            $organization = $this->createOrganization->handle(
                data: CreateOrganizationData::from([
                    'name' => $data->organizationName,
                ])
            );

            $this->createSystemTimeOffTypes->handle(
                organization: $organization,
            );

            $user = $this->createUser->handle(
                data: CreateUserData::from([
                    'organization_id' => $organization->id,
                    'name' => $data->name,
                    'email' => $data->email,
                    'password' => $data->password,
                ])
            );

            $user->assignRole(UserRole::Owner);

            return $user;

        });
    }
}
