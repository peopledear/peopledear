<?php

declare(strict_types=1);

namespace App\Filament\Resources\Prices\Pages;

use App\Filament\Resources\Prices\PriceResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @codeCoverageIgnore
 */
final class CreatePrice extends CreateRecord
{
    protected static string $resource = PriceResource::class;
}
