<?php

declare(strict_types=1);

namespace App\Actions\Location;

use App\Actions\Address\DeleteAddress;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteLocation
{
    public function __construct(
        private DeleteAddress $deleteAddress,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(Location $location): void
    {
        DB::transaction(function () use ($location): void {
            $this->deleteAddress->handle($location);

            $location->delete();
        });
    }
}
