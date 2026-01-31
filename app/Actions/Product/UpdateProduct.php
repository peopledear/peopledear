<?php

declare(strict_types=1);

namespace App\Actions\Product;

use App\Actions\SyncProductToStripe;
use App\Data\Billing\UpdateProductData;
use App\Models\Product;

final readonly class UpdateProduct
{
    public function __construct(
        private SyncProductToStripe $syncProductToStripe,
    ) {}

    /**
     * Update a product and sync changes to Stripe.
     */
    public function handle(Product $product, UpdateProductData $data): Product
    {
        $product->update($data->toArray());

        return $this->syncProductToStripe->handle($product->refresh());
    }
}
