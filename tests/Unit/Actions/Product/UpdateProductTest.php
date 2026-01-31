<?php

declare(strict_types=1);

use App\Actions\Product\UpdateProduct;
use App\Actions\SyncProductToStripe;
use App\Data\Billing\UpdateProductData;
use App\Models\Product;
use Stripe\Service\ProductService;
use Stripe\StripeClient;

beforeEach(function (): void {
    $this->stripeClient = Mockery::mock(StripeClient::class);
    $this->productService = Mockery::mock(ProductService::class);
    $this->stripeClient->products = $this->productService;

    $syncProductToStripe = new SyncProductToStripe($this->stripeClient);
    $this->action = new UpdateProduct($syncProductToStripe);
});

test('updates product and syncs to stripe', function (): void {
    $product = Product::factory()->create([
        'name' => 'Old Name',
        'description' => 'Old description',
        'is_active' => true,
        'stripe_product_id' => 'prod_existing123',
    ]);

    $data = new UpdateProductData(
        name: 'New Name',
        description: 'New description',
        is_active: false,
    );

    $this->productService
        ->shouldReceive('update')
        ->once()
        ->with('prod_existing123', Mockery::on(fn (array $params): bool => $params['name'] === 'New Name'
            && $params['description'] === 'New description'
            && $params['active'] === false
        ))
        ->andReturn((object) ['id' => 'prod_existing123']);

    $result = $this->action->handle($product, $data);

    expect($result)
        ->toBeInstanceOf(Product::class)
        ->and($result->name)->toBe('New Name')
        ->and($result->description)->toBe('New description')
        ->and($result->is_active)->toBeFalse();
});

test('updates only provided fields', function (): void {
    $product = Product::factory()->create([
        'name' => 'Original Name',
        'description' => 'Original description',
        'is_active' => true,
        'stripe_product_id' => 'prod_partial123',
    ]);

    $data = UpdateProductData::from([
        'name' => 'Updated Name',
    ]);

    $this->productService
        ->shouldReceive('update')
        ->once()
        ->with('prod_partial123', Mockery::on(fn (array $params): bool => $params['name'] === 'Updated Name'
            && $params['description'] === 'Original description'
            && $params['active'] === true
        ))
        ->andReturn((object) ['id' => 'prod_partial123']);

    $result = $this->action->handle($product, $data);

    expect($result->name)->toBe('Updated Name')
        ->and($result->description)->toBe('Original description')
        ->and($result->is_active)->toBeTrue();
});

test('can set description to null', function (): void {
    $product = Product::factory()->create([
        'name' => 'Product',
        'description' => 'Has description',
        'is_active' => true,
        'stripe_product_id' => 'prod_nulldesc123',
    ]);

    $data = new UpdateProductData(
        name: 'Product',
        description: null,
        is_active: true,
    );

    $this->productService
        ->shouldReceive('update')
        ->once()
        ->with('prod_nulldesc123', Mockery::on(fn (array $params): bool => ! array_key_exists('description', $params)
        ))
        ->andReturn((object) ['id' => 'prod_nulldesc123']);

    $result = $this->action->handle($product, $data);

    expect($result->description)->toBeNull();
});

test('creates stripe product when no stripe_product_id exists', function (): void {
    $product = Product::factory()->create([
        'name' => 'New Product',
        'description' => 'New description',
        'is_active' => true,
        'stripe_product_id' => null,
    ]);

    $data = new UpdateProductData(
        name: 'Updated Product',
        description: 'Updated description',
        is_active: true,
    );

    $stripeProduct = (object) ['id' => 'prod_new123'];

    $this->productService
        ->shouldReceive('create')
        ->once()
        ->with(Mockery::on(fn (array $params): bool => $params['name'] === 'Updated Product'
            && $params['description'] === 'Updated description'
            && $params['active'] === true
        ))
        ->andReturn($stripeProduct);

    $result = $this->action->handle($product, $data);

    expect($result->name)->toBe('Updated Product')
        ->and($result->stripe_product_id)->toBe('prod_new123');
});
