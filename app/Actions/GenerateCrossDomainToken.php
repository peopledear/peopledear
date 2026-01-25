<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\CrossDomainAuthToken;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Str;

final readonly class GenerateCrossDomainToken
{
    public function handle(User $user, Organization $organization, string $intended): CrossDomainAuthToken
    {
        return CrossDomainAuthToken::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'nonce' => Str::uuid()->toString(),
            'intended' => $intended,
            'expires_at' => now()->addMinutes(5),
        ]);
    }
}
