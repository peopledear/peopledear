<?php

declare(strict_types=1);

namespace App\Actions\Address;

use App\Contracts\Addressable;
use App\Data\PeopleDear\Address\CreateAddressData;
use App\Models\Address;

final readonly class CreateAddress
{
    /**
     * Create an address for an addressable model.
     */
    public function handle(Addressable $addressable, CreateAddressData $data): Address
    {
        /** @var Address $address */
        $address = $addressable
            ->address()
            ->create($data->toArray());

        return $address;
    }
}
