<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateUserData;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final readonly class AcceptInvitation
{
    public function __construct(
        private CreateUser $createUser
    ) {}

    public function handle(Invitation $invitation, string $name, string $password): User
    {
        return DB::transaction(function () use ($invitation, $name, $password): User {
            $userData = CreateUserData::from([
                'name' => $name,
                'email' => $invitation->email,
                'password' => $password,
                'role_id' => $invitation->role_id,
            ]);

            $user = $this->createUser->handle($userData);

            $invitation->update(['accepted_at' => now()]);

            Auth::login($user);

            return $user;
        });
    }
}
