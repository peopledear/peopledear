<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

enum Disk: string
{
    case Local = 'local';
    case Public = 'public';
    case S3Private = 's3-private';
    case S3Public = 's3-public';

    public function storage(): Filesystem
    {
        return Storage::disk($this->value);
    }

    public function fake(): void
    {
        Storage::fake($this->value);
    }
}
