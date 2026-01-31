<?php

declare(strict_types=1);

namespace App\Actions\Product;

use App\Models\Product;
use Stripe\StripeClient;

final readonly class DeactivateProductInStripe
{
    public function __construct(
        private StripeClient $stripe,
    ) {}

    /**
     * Deactivate a product in Stripe by setting active to false.
     */
    public function handle(Product $product): void
    {
        if ($product->stripe_product_id === null) {
            return;
        }

        $this->stripe->products->update($product->stripe_product_id, [
            'active' => false,
        ]);
    }
}
