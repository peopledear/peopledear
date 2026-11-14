<?php

declare(strict_types=1);

use App\Actions\UpsertProductFromStripe;
use App\Data\StripeProductData;
use App\Models\Product;

test('creates product from stripe data',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var UpsertProductFromStripe $action */
        $action = app(UpsertProductFromStripe::class);

        $data = new StripeProductData(
            'prod_test_123',
            'Test Product',
            'Description',
            true,
        );

        $product = $action->handle($data);

        expect($product->stripe_product_id)->toBe('prod_test_123')
            ->and($product->name)->toBe('Test Product')
            ->and($product->description)->toBe('Description')
            ->and($product->is_active)->toBeTrue();
    });

test('updates existing product with matching stripe id',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var UpsertProductFromStripe $action */
        $action = app(UpsertProductFromStripe::class);

        /** @var Product $existing */
        $existing = Product::factory()->createQuietly([
            'name' => 'Old Name',
            'description' => 'Old Description',
            'stripe_product_id' => 'prod_same',
            'is_active' => false,
        ]);

        $data = new StripeProductData(
            'prod_same',
            'New Name',
            null,
            true,
        );

        $product = $action->handle($data);

        expect($product->id)->toBe($existing->id)
            ->and($product->name)->toBe('New Name')
            ->and($product->description)->toBeNull()
            ->and($product->is_active)->toBeTrue();
    });
