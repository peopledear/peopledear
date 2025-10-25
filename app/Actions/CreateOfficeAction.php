<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateOfficeData;
use App\Models\Office;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;

final readonly class CreateOfficeAction
{
    public function __construct(
        private CreateAddressAction $createAddress,
    ) {}

    /**
     * Create an office with address for the given organization.
     */
    public function handle(CreateOfficeData $data, Organization $organization): Office
    {
        return DB::transaction(function () use ($data, $organization): Office {

            /** @var Office $office */
            $office = Office::query()->create([
                'organization_id' => $organization->id,
                ...$data->except('address')->toArray(),
            ]);

            $this->createAddress->handle($office, $data->address);

            return $office->refresh();
        });
    }
}
