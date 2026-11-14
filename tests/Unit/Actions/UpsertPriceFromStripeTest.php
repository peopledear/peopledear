<?php

declare(strict_types=1);

use App\Actions\UpsertPriceFromStripe;
use App\Actions\UpsertProductFromStripe;
use App\Data\StripePriceData;
use App\Data\StripeProductData;
use App\Models\Price;
use App\Models\Product;

test('creates price and associates product from stripe data',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var UpsertProductFromStripe $upsertProduct */
        $upsertProduct = app(UpsertProductFromStripe::class);

        /** @var UpsertPriceFromStripe $action */
        $action = app(UpsertPriceFromStripe::class);

        $productData = new StripeProductData(
            'prod_price_create',
            'Product With Price',
            null,
            true,
        );

        $product = $upsertProduct->handle($productData);

        $priceData = new StripePriceData(
            'price_new_123',
            'prod_price_create',
            1500,
            'eur',
            'month',
            true,
        );

        $price = $action->handle($priceData, $product);

        expect($price->stripe_price_id)->toBe('price_new_123')
            ->and($price->product_id)->toBe($product->id)
            ->and($price->amount)->toBe(1500)
            ->and($price->currency)->toBe('eur')
            ->and($price->interval)->toBe('month')
            ->and($price->is_active)->toBeTrue();
    });

test('updates existing price when stripe id matches',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var UpsertProductFromStripe $upsertProduct */
        $upsertProduct = app(UpsertProductFromStripe::class);

        /** @var UpsertPriceFromStripe $action */
        $action = app(UpsertPriceFromStripe::class);

        /** @var Product $product */
        $product = $upsertProduct->handle(new StripeProductData(
            'prod_price_update',
            'Product',
            null,
            true,
        ));

        /** @var Price $existing */
        $existing = Price::factory()->createQuietly([
            'product_id' => $product->id,
            'stripe_price_id' => 'price_existing',
            'amount' => 999,
            'currency' => 'eur',
            'interval' => 'month',
            'is_active' => false,
        ]);

        $priceData = new StripePriceData(
            'price_existing',
            'prod_price_update',
            2500,
            'usd',
            'one_time',
            true,
        );

        $price = $action->handle($priceData, $product);

        expect($price->id)->toBe($existing->id)
            ->and($price->amount)->toBe(2500)
            ->and($price->currency)->toBe('usd')
            ->and($price->interval)->toBe('one_time')
            ->and($price->is_active)->toBeTrue();
    });
