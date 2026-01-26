<?php

declare(strict_types=1);

use App\Actions\SyncPriceToStripe;
use App\Actions\SyncProductToStripe;
use App\Models\Price;
use App\Models\Product;
use Stripe\Service\PriceService;
use Stripe\Service\ProductService;
use Stripe\StripeClient;

beforeEach(function (): void {
    $this->stripeClient = Mockery::mock(StripeClient::class);
    $this->priceService = Mockery::mock(PriceService::class);
    $this->productService = Mockery::mock(ProductService::class);
    $this->stripeClient->prices = $this->priceService;
    $this->stripeClient->products = $this->productService;

    $this->syncProductToStripe = new SyncProductToStripe($this->stripeClient);
    $this->action = new SyncPriceToStripe($this->stripeClient, $this->syncProductToStripe);
});

test('creates price when product already synced', function (): void {
    $product = Product::factory()->create([
        'stripe_product_id' => 'prod_existing123',
    ]);
    $price = Price::factory()->for($product)->create([
        'stripe_price_id' => null,
        'interval' => 'month',
        'amount' => 1000,
        'currency' => 'eur',
    ]);

    $stripePrice = (object) ['id' => 'price_new123'];

    $this->priceService
        ->shouldReceive('create')
        ->once()
        ->with(Mockery::on(fn (array $params): bool => $params['product'] === 'prod_existing123'
            && $params['unit_amount'] === 1000
            && $params['currency'] === 'eur'
            && $params['recurring']['interval'] === 'month'
        ))
        ->andReturn($stripePrice);

    $result = $this->action->handle($price);

    expect($result->stripe_price_id)->toBe('price_new123');
});

test('syncs product first when product not synced', function (): void {
    $product = Product::factory()->create([
        'stripe_product_id' => null,
    ]);
    $price = Price::factory()->for($product)->create([
        'stripe_price_id' => null,
    ]);

    $stripeProduct = (object) ['id' => 'prod_new123'];
    $stripePrice = (object) ['id' => 'price_new123'];

    $this->productService
        ->shouldReceive('create')
        ->once()
        ->andReturn($stripeProduct);

    $this->priceService
        ->shouldReceive('create')
        ->once()
        ->with(Mockery::on(fn (array $params): bool => $params['product'] === 'prod_new123'))
        ->andReturn($stripePrice);

    $result = $this->action->handle($price);

    expect($result->stripe_price_id)->toBe('price_new123');
    expect($result->product->stripe_product_id)->toBe('prod_new123');
});

test('archives old price when updating existing price', function (): void {
    $product = Product::factory()->create([
        'stripe_product_id' => 'prod_existing123',
    ]);
    $price = Price::factory()->for($product)->create([
        'stripe_price_id' => 'price_old123',
    ]);

    $stripePrice = (object) ['id' => 'price_new123'];

    $this->priceService
        ->shouldReceive('update')
        ->once()
        ->with('price_old123', ['active' => false])
        ->andReturn((object) ['id' => 'price_old123']);

    $this->priceService
        ->shouldReceive('create')
        ->once()
        ->andReturn($stripePrice);

    $result = $this->action->handle($price);

    expect($result->stripe_price_id)->toBe('price_new123');
});
