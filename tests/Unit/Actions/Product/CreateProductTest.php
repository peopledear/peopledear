<?php

declare(strict_types=1);

use App\Actions\Product\CreateProduct;
use App\Actions\SyncProductToStripe;
use App\Data\Billing\CreateProductData;
use App\Models\Product;
use Stripe\Service\ProductService;
use Stripe\StripeClient;

beforeEach(function (): void {
    $this->stripeClient = Mockery::mock(StripeClient::class);
    $this->productService = Mockery::mock(ProductService::class);
    $this->stripeClient->products = $this->productService;

    $syncProductToStripe = new SyncProductToStripe($this->stripeClient);
    $this->action = new CreateProduct($syncProductToStripe);
});

test('creates product and syncs to stripe', function (): void {
    $data = new CreateProductData(
        name: 'Premium Plan',
        description: 'A premium subscription plan',
        is_active: true,
    );

    $stripeProduct = (object) ['id' => 'prod_test123'];

    $this->productService
        ->shouldReceive('create')
        ->once()
        ->with(Mockery::on(fn (array $params): bool => $params['name'] === 'Premium Plan'
            && $params['description'] === 'A premium subscription plan'
            && $params['active'] === true
        ))
        ->andReturn($stripeProduct);

    $result = $this->action->handle($data);

    expect($result)
        ->toBeInstanceOf(Product::class)
        ->and($result->name)->toBe('Premium Plan')
        ->and($result->description)->toBe('A premium subscription plan')
        ->and($result->is_active)->toBeTrue()
        ->and($result->stripe_product_id)->toBe('prod_test123');
});

test('creates product with null description', function (): void {
    $data = new CreateProductData(
        name: 'Basic Plan',
        description: null,
        is_active: true,
    );

    $stripeProduct = (object) ['id' => 'prod_basic123'];

    $this->productService
        ->shouldReceive('create')
        ->once()
        ->with(Mockery::on(fn (array $params): bool => $params['name'] === 'Basic Plan'
            && ! array_key_exists('description', $params)
        ))
        ->andReturn($stripeProduct);

    $result = $this->action->handle($data);

    expect($result->description)->toBeNull()
        ->and($result->stripe_product_id)->toBe('prod_basic123');
});

test('creates inactive product', function (): void {
    $data = new CreateProductData(
        name: 'Archived Plan',
        description: 'No longer available',
        is_active: false,
    );

    $stripeProduct = (object) ['id' => 'prod_archived123'];

    $this->productService
        ->shouldReceive('create')
        ->once()
        ->with(Mockery::on(fn (array $params): bool => $params['active'] === false
        ))
        ->andReturn($stripeProduct);

    $result = $this->action->handle($data);

    expect($result->is_active)->toBeFalse()
        ->and($result->stripe_product_id)->toBe('prod_archived123');
});
