<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

use function assert;

final readonly class DeleteUserAvatar
{
    public function handle(User $user): void
    {
        if ($path = $user->getRawOriginal('avatar')) {
            assert(is_string($path));
            Storage::disk('public')->delete($path);
            $user->update(['avatar' => null]);
        }
    }
}
