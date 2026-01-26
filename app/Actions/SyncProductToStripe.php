<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Product;
use Stripe\StripeClient;

final readonly class SyncProductToStripe
{
    public function __construct(
        private StripeClient $stripe,
    ) {}

    public function handle(Product $product): Product
    {
        $params = [
            'name' => $product->name,
            'active' => $product->is_active,
        ];

        if ($product->description !== null) {
            $params['description'] = $product->description;
        }

        if ($product->stripe_product_id !== null) {
            $this->stripe->products->update($product->stripe_product_id, $params);
        } else {
            $stripeProduct = $this->stripe->products->create($params);
            $product->update(['stripe_product_id' => $stripeProduct->id]);
        }

        return $product->refresh();
    }
}
