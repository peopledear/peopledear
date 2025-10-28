<?php

declare(strict_types=1);

namespace App\Actions\Address;

use App\Contracts\Addressable;

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
