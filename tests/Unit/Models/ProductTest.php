<?php

declare(strict_types=1);

use App\Models\Price;
use App\Models\Product;

test('has many prices', function (): void {
    $product = Product::factory()->create();
    Price::factory()->for($product)->count(3)->create();

    expect($product->prices)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Price::class);
});

test('to array', function (): void {
    $product = Product::factory()->create()->refresh();

    expect(array_keys($product->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'name',
            'description',
            'stripe_product_id',
            'is_active',
        ]);
});
