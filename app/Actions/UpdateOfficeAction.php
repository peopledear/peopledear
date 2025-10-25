<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\UpdateOfficeData;
use App\Models\Office;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Optional;

final readonly class UpdateOfficeAction
{
    public function __construct(
        private UpdateAddressAction $updateAddress,
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
