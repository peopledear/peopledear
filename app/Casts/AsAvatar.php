<?php

declare(strict_types=1);

namespace App\Casts;

use App\Data\AvatarData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use function array_key_exists;

/**
 * @implements CastsAttributes<AvatarData, mixed>
 */
final class AsAvatar implements CastsAttributes
{
    /**
     * {@inheritDoc}
     *
     * @param  array<string, string>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): AvatarData
    {
        return new AvatarData(
            path: $model->hasAttribute('avatar') && array_key_exists('avatar', $attributes)
                ? $attributes['avatar']
                : null,
            src: $model->hasAttribute('avatar') && array_key_exists('avatar', $attributes) && $attributes['avatar']
                ? Storage::disk('public')->url($attributes['avatar'])
                : null,
            alt: $model->hasAttribute('name')
                ? $attributes['name']
                : null,
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }
}
