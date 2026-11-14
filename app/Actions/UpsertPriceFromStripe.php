<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\StripePriceData;
use App\Models\Price;
use App\Models\Product;

final readonly class UpsertPriceFromStripe
{
    public function handle(StripePriceData $data, Product $product): Price
    {
        /** @var Price $price */
        $price = Price::query()->firstOrNew([
            'stripe_price_id' => $data->id,
        ]);

        $price->product()->associate($product);
        $price->amount = $data->unit_amount;
        $price->currency = $data->currency;
        $price->interval = $data->interval;
        $price->is_active = $data->active;
        $price->stripe_price_id = $data->id;

        $price->save();

        return $price->refresh();
    }
}
