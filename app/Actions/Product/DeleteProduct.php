<?php

declare(strict_types=1);

namespace App\Actions\Product;

use App\Models\Product;

final readonly class DeleteProduct
{
    public function __construct(
        private DeactivateProductInStripe $deactivateProductInStripe,
    ) {}

    /**
     * Delete a product locally after deactivating it in Stripe.
     */
    public function handle(Product $product): void
    {
        $this->deactivateProductInStripe->handle($product);

        $product->delete();
    }
}
