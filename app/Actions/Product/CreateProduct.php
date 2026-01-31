<?php

declare(strict_types=1);

namespace App\Actions\Product;

use App\Actions\SyncProductToStripe;
use App\Data\Billing\CreateProductData;
use App\Models\Product;

final readonly class CreateProduct
{
    public function __construct(
        private SyncProductToStripe $syncProductToStripe,
    ) {}

    /**
     * Create a product and sync it to Stripe.
     */
    public function handle(CreateProductData $data): Product
    {
        /** @var Product $product */
        $product = Product::query()->create($data->toArray());

        return $this->syncProductToStripe->handle($product);
    }
}
