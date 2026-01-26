<?php

declare(strict_types=1);

use App\Actions\SyncProductToStripe;
use App\Models\Product;
use Stripe\Service\ProductService;
use Stripe\StripeClient;

beforeEach(function (): void {
    $this->stripeClient = Mockery::mock(StripeClient::class);
    $this->productService = Mockery::mock(ProductService::class);
    $this->stripeClient->products = $this->productService;

    $this->action = new SyncProductToStripe($this->stripeClient);
});

test('creates new product in stripe when no stripe_product_id exists', function (): void {
    $product = Product::factory()->create([
        'stripe_product_id' => null,
        'description' => 'Test description',
    ]);

    $stripeProduct = (object) ['id' => 'prod_test123'];

    $this->productService
        ->shouldReceive('create')
        ->once()
        ->with(Mockery::on(fn (array $params): bool => $params['name'] === $product->name
            && $params['active'] === $product->is_active
            && $params['description'] === 'Test description'
        ))
        ->andReturn($stripeProduct);

    $result = $this->action->handle($product);

    expect($result->stripe_product_id)->toBe('prod_test123');
});

test('updates existing product in stripe when stripe_product_id exists', function (): void {
    $product = Product::factory()->create([
        'stripe_product_id' => 'prod_existing123',
        'description' => 'Updated description',
    ]);

    $this->productService
        ->shouldReceive('update')
        ->once()
        ->with('prod_existing123', Mockery::on(fn (array $params): bool => $params['name'] === $product->name
            && $params['active'] === $product->is_active
            && $params['description'] === 'Updated description'
        ))
        ->andReturn((object) ['id' => 'prod_existing123']);

    $result = $this->action->handle($product);

    expect($result->stripe_product_id)->toBe('prod_existing123');
});

test('excludes description when null', function (): void {
    $product = Product::factory()->create([
        'stripe_product_id' => null,
        'description' => null,
    ]);

    $stripeProduct = (object) ['id' => 'prod_nodesc123'];

    $this->productService
        ->shouldReceive('create')
        ->once()
        ->with(Mockery::on(fn (array $params): bool => $params['name'] === $product->name
            && $params['active'] === $product->is_active
            && ! array_key_exists('description', $params)
        ))
        ->andReturn($stripeProduct);

    $result = $this->action->handle($product);

    expect($result->stripe_product_id)->toBe('prod_nodesc123');
});
