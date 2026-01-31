<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\Pages;

use App\Actions\Product\CreateProduct as CreateProductAction;
use App\Data\Billing\CreateProductData;
use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @codeCoverageIgnore
 */
final class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    private CreateProductAction $createProduct;

    public function boot(CreateProductAction $createProduct): void
    {
        $this->createProduct = $createProduct;
    }

    protected function handleRecordCreation(array $data): \App\Models\Product
    {
        return $this->createProduct->handle(CreateProductData::from($data));
    }
}
