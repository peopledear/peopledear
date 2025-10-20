<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateUserData;
use App\Models\User;

final class CreateUser
{
    public function handle(CreateUserData $data): User
    {
        return User::query()
            ->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
                'role_id' => $data->role_id,
                'email_verified_at' => now(),
            ]);
    }
}
