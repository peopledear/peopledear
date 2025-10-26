<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\Addressable;
use App\Data\CreateAddressData;
use App\Models\Address;
use Illuminate\Database\Eloquent\Model;

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
