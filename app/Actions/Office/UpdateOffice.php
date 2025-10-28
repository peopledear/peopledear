<?php

declare(strict_types=1);

namespace App\Actions\Office;

use App\Actions\Address\UpdateAddress;
use App\Data\PeopleDear\Office\UpdateOfficeData;
use App\Models\Office;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Optional;

final readonly class UpdateOffice
{
    public function __construct(
        private UpdateAddress $updateAddress,
    ) {}

    /**
     * Update an office and its address.
     */
    public function handle(Office $office, UpdateOfficeData $data): Office
    {
        return DB::transaction(function () use ($office, $data): Office {
            $office->update($data->except('address')->toArray());

            if (! ($data->address instanceof Optional)) {
                $this->updateAddress->handle($office, $data->address);
            }

            return $office->refresh();
        });
    }
}
