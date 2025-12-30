<?php

declare(strict_types=1);

namespace App\Actions\Office;

use App\Actions\Address\CreateAddress;
use App\Data\PeopleDear\Office\CreateOfficeData;
use App\Models\Office;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateOffice
{
    public function __construct(
        private CreateAddress $createAddress,
    ) {}

    /**
     * @throws Throwable
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
