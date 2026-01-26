<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Price;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

final readonly class SyncPriceToStripe
{
    public function __construct(
        private StripeClient $stripe,
        private SyncProductToStripe $syncProductToStripe,
    ) {}

    /**
     * @throws ApiErrorException
     */
    public function handle(Price $price): Price
    {
        $product = $price->product;
        if ($product->stripe_product_id === null) {
            $product = $this->syncProductToStripe->handle($product);
        }

        if ($price->stripe_price_id !== null) {
            $this->stripe->prices->update($price->stripe_price_id, [
                'active' => false,
            ]);
        }

        /** @var string $stripeProductId */
        $stripeProductId = $product->stripe_product_id;

        $stripePrice = $this->stripe->prices->create([
            'product' => $stripeProductId,
            'unit_amount' => $price->amount,
            'currency' => $price->currency,
            'recurring' => [
                'interval' => $price->interval,
            ],
            'active' => $price->is_active,
        ]);

        $price->update(['stripe_price_id' => $stripePrice->id]);

        return $price->refresh();
    }
}
