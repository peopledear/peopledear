<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\StripeProductData;
use App\Models\Product;

final readonly class UpsertProductFromStripe
{
    public function handle(StripeProductData $data): Product
    {
        /** @var Product $product */
        $product = Product::query()->firstOrNew([
            'stripe_product_id' => $data->id,
        ]);

        $product->name = $data->name;
        $product->description = $data->description;
        $product->is_active = $data->active;
        $product->stripe_product_id = $data->id;

        $product->save();

        return $product->refresh();
    }
}
