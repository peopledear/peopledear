<?php

declare(strict_types=1);

namespace App\Actions\Address;

use App\Contracts\Addressable;
use App\Data\PeopleDear\Address\UpdateAddressData;
use App\Models\Address;

final readonly class UpdateAddress
{
    /**
     * Update the address of an addressable model.
     */
    public function handle(Addressable $addressable, UpdateAddressData $data): Address
    {
        /** @var Address $address */
        $address = $addressable->address;

        $address->update($data->toArray());

        return $address->refresh();
    }
}
