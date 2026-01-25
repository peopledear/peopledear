<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Disk;
use App\Models\User;

use function assert;

final readonly class DeleteUserAvatar
{
    public function handle(User $user): void
    {
        if ($path = $user->getRawOriginal('avatar')) {
            assert(is_string($path));
            Disk::S3Public->storage()->delete($path);
            $user->update(['avatar' => null]);
        }
    }
}
