<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Disk;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

use function time;

final readonly class StoreUserAvatar
{
    public function __construct(
        private DeleteUserAvatar $deleteUserAvatar
    ) {}

    public function handle(User $user, UploadedFile $file): string
    {
        if ($user->getRawOriginal('avatar')) {
            $this->deleteUserAvatar->handle($user);
        }

        $manager = new ImageManager(new Driver);

        // Read and resize image
        $image = $manager->read($file->getRealPath());

        $image->scale(width: 400, height: 400);

        // Convert to WebP and encode
        $encoded = $image->toWebp(quality: 85);

        // Generate filename and path
        $filename = $user->id.'.'.time().'.webp';
        $path = 'avatars/'.$filename;

        // Save to public disk
        Disk::S3Public->storage()->put($path, $encoded->toString());

        // Update user's avatar
        $user->update(['avatar' => $path]);

        return $path;
    }
}
