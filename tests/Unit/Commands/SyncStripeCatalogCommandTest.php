<?php

declare(strict_types=1);

use App\Models\Price;
use App\Models\Product;
use Stripe\Service\PriceService;
use Stripe\Service\ProductService;
use Stripe\StripeClient;

use function Pest\Laravel\artisan;

test('syncs products and prices from stripe',
    /**
     * @throws Throwable
     */
    function (): void {
        $productService = Mockery::mock(ProductService::class);
        $priceService = Mockery::mock(PriceService::class);
        $stripeClient = Mockery::mock(StripeClient::class);
        $stripeClient->products = $productService;
        $stripeClient->prices = $priceService;

        app()->instance(StripeClient::class, $stripeClient);

        $productWithPrices = (object) [
            'id' => 'prod_1',
            'name' => 'First Product',
            'description' => null,
            'active' => true,
            'prices' => (object) [
                'data' => [
                    (object) [
                        'id' => 'price_1',
                        'product' => 'prod_1',
                        'unit_amount' => 1000,
                        'currency' => 'eur',
                        'recurring' => (object) ['interval' => 'month'],
                        'active' => true,
                    ],
                ],
            ],
        ];

        $productWithoutPrices = (object) [
            'id' => 'prod_2',
            'name' => 'Second Product',
            'description' => 'Desc',
            'active' => false,
        ];

        $additionalPrice = (object) [
            'id' => 'price_2',
            'product' => 'prod_2',
            'unit_amount' => 2000,
            'currency' => 'usd',
            'recurring' => (object) ['interval' => 'year'],
            'active' => true,
        ];

        $productService
            ->shouldReceive('all')
            ->once()
            ->with([
                'limit' => 100,
                'starting_after' => null,
                'expand' => ['data.prices'],
            ])
            ->andReturn((object) [
                'data' => [$productWithPrices, $productWithoutPrices],
                'has_more' => false,
            ]);

        $priceService
            ->shouldReceive('all')
            ->once()
            ->with([
                'product' => 'prod_2',
                'limit' => 100,
            ])
            ->andReturn((object) [
                'data' => [$additionalPrice],
            ]);

        artisan('billing:sync-stripe-catalog')
            ->assertExitCode(0);

        $firstProduct = Product::query()->where('stripe_product_id', 'prod_1')->first();
        $secondProduct = Product::query()->where('stripe_product_id', 'prod_2')->first();

        expect($firstProduct)->not()->toBeNull()
            ->and($firstProduct?->name)->toBe('First Product')
            ->and($firstProduct?->is_active)->toBeTrue();

        expect($secondProduct)->not()->toBeNull()
            ->and($secondProduct?->name)->toBe('Second Product')
            ->and($secondProduct?->is_active)->toBeFalse();

        /** @var Price|null $firstPrice */
        $firstPrice = Price::query()->where('stripe_price_id', 'price_1')->first();
        /** @var Price|null $secondPrice */
        $secondPrice = Price::query()->where('stripe_price_id', 'price_2')->first();

        expect($firstPrice)->not()->toBeNull()
            ->and($firstPrice?->amount)->toBe(1000)
            ->and($firstPrice?->interval)->toBe('month');

        expect($secondPrice)->not()->toBeNull()
            ->and($secondPrice?->amount)->toBe(2000)
            ->and($secondPrice?->interval)->toBe('year')
            ->and($secondPrice?->product_id)->toBe($secondProduct?->id);
    });

test('supports dry run without persisting changes',
    /**
     * @throws Throwable
     */
    function (): void {
        $productService = Mockery::mock(ProductService::class);
        $priceService = Mockery::mock(PriceService::class);
        $stripeClient = Mockery::mock(StripeClient::class);
        $stripeClient->products = $productService;
        $stripeClient->prices = $priceService;

        app()->instance(StripeClient::class, $stripeClient);

        $product = (object) [
            'id' => 'prod_dry',
            'name' => 'Dry Product',
            'description' => null,
            'active' => true,
            'prices' => (object) [
                'data' => [
                    (object) [
                        'id' => 'price_dry',
                        'product' => 'prod_dry',
                        'unit_amount' => 500,
                        'currency' => 'eur',
                        'recurring' => (object) ['interval' => 'month'],
                        'active' => true,
                    ],
                ],
            ],
        ];

        $productService
            ->shouldReceive('all')
            ->once()
            ->with([
                'limit' => 100,
                'starting_after' => null,
                'expand' => ['data.prices'],
            ])
            ->andReturn((object) [
                'data' => [$product],
                'has_more' => false,
            ]);

        artisan('billing:sync-stripe-catalog', ['--dry-run' => true])
            ->assertExitCode(0);

        expect(Product::query()->where('stripe_product_id', 'prod_dry')->exists())->toBeFalse()
            ->and(Price::query()->where('stripe_price_id', 'price_dry')->exists())->toBeFalse();
    });
