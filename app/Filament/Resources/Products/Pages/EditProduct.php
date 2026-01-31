<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\Pages;

use App\Actions\Product\UpdateProduct as UpdateProductAction;
use App\Data\Billing\UpdateProductData;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

/**
 * @codeCoverageIgnore
 */
final class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    private UpdateProductAction $updateProduct;

    public function boot(UpdateProductAction $updateProduct): void
    {
        $this->updateProduct = $updateProduct;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Product
    {
        /** @var Product $record */
        return $this->updateProduct->handle($record, UpdateProductData::from($data));
    }
}
