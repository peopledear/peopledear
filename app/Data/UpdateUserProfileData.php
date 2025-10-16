<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Spatie\LaravelData\Data;

final class UpdateUserProfileData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?UploadedFile $avatar = null,
    ) {}

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.auth()->id()],
            'avatar' => [
                'nullable',
                File::image()
                    ->max(2 * 1024)
                    ->dimensions(
                        Rule::dimensions()
                            ->minWidth(200)
                            ->minHeight(200)
                            ->maxWidth(2000)
                            ->maxHeight(2000)
                    ),
            ],
        ];
    }
}
