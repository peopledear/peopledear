<?php

declare(strict_types=1);

namespace App\Actions\Office;

use App\Actions\Address\DeleteAddress;
use App\Models\Office;
use Illuminate\Support\Facades\DB;

final readonly class DeleteOffice
{
    public function __construct(
        private DeleteAddress $deleteAddress,
    )
    {
    }

    /**
     * Delete an office and its address.
     */
    public function handle(Office $office): void
    {
        DB::transaction(function () use ($office): void {
            $this->deleteAddress->handle($office);

            $office->delete();
        });
    }
}
