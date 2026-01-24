<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Data\PeopleDear\CreateUserData;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

final readonly class CreateUser
{
    public function handle(CreateUserData $data): User
    {
        $user = User::query()->create([
            'organization_id' => $data->organization_id,
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ]);

        event(new Registered($user));

        return $user;
    }
}
