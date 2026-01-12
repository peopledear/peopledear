<?php

declare(strict_types=1);

namespace App\Actions;

use App\Actions\Organization\CreateOrganization;
use App\Actions\User\CreateUser;
use App\Data\PeopleDear\CreateRegistrationData;
use App\Data\PeopleDear\Organization\CreateOrganizationData;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class NewAccountRegistration
{
    public function __construct(
        private CreateOrganization $createOrganization,
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

            $user = $this->createUser->handle(
                attributes: [
                    'organization_id' => $organization->id,
                    'name' => $data->name,
                    'email' => $data->email,
                ],
                password: $data->password
            );

            $user->assignRole(UserRole::Owner);

            return $user;

        });
    }
}
