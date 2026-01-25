<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

use function assert;
use function time;

final readonly class StoreUserAvatar
{
    public function handle(User $user, UploadedFile $file): string
    {
        // Delete old avatar if exists
        if ($filename = $user->getRawOriginal('avatar')) {
            assert(is_string($filename));
            Storage::disk('public')->delete($filename);
        }

        $manager = new ImageManager(new Driver);

        // Read and resize image
        $image = $manager->read($file->getRealPath());

        // Resize to max 400x400 while maintaining aspect ratio
        $image->scale(width: 400, height: 400);

        // Convert to WebP and encode
        $encoded = $image->toWebp(quality: 85);

        // Generate filename and path
        $filename = $user->id.'.'.time().'.webp';
        $path = 'avatars/'.$filename;

        // Save to public disk
        Storage::disk('public')->put($path, (string) $encoded);

        // Update user's avatar
        $user->update(['avatar' => $path]);

        return $path;
    }
}
