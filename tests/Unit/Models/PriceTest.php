<?php

declare(strict_types=1);

use App\Models\Price;
use App\Models\Product;

test('belongs to product', function (): void {
    $product = Product::factory()->create();
    $price = Price::factory()->for($product)->create();

    expect($price->product)
        ->toBeInstanceOf(Product::class)
        ->id->toBe($product->id);
});

test('to array', function (): void {
    $product = Product::factory()->create();
    $price = Price::factory()->for($product)->create()->refresh();

    expect(array_keys($price->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'product_id',
            'stripe_price_id',
            'interval',
            'amount',
            'currency',
            'is_active',
        ]);
});
