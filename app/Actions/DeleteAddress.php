<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\Addressable;
use App\Models\Address;
use Illuminate\Database\Eloquent\Model;

final readonly class DeleteAddress
{
    /**
     * Delete the address of an addressable model.
     */
    public function handle(Addressable $addressable): void
    {
        $addressable->address?->delete();

    }
}
