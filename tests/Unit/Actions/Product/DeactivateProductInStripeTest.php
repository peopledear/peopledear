<?php

declare(strict_types=1);

use App\Actions\Product\DeactivateProductInStripe;
use App\Models\Product;
use Stripe\Service\ProductService;
use Stripe\StripeClient;

beforeEach(function (): void {
    $this->stripeClient = Mockery::mock(StripeClient::class);
    $this->productService = Mockery::mock(ProductService::class);
    $this->stripeClient->products = $this->productService;

    $this->action = new DeactivateProductInStripe($this->stripeClient);
});

test('deactivates product in stripe when stripe_product_id exists', function (): void {
    $product = Product::factory()->create([
        'stripe_product_id' => 'prod_test123',
        'is_active' => true,
    ]);

    $this->productService
        ->shouldReceive('update')
        ->once()
        ->with('prod_test123', ['active' => false])
        ->andReturn((object) ['id' => 'prod_test123', 'active' => false]);

    $this->action->handle($product);

    // Assert the Stripe API was called (verified by mock expectation)
    expect(true)->toBeTrue();
});

test('does nothing when stripe_product_id is null', function (): void {
    $product = Product::factory()->create([
        'stripe_product_id' => null,
        'is_active' => true,
    ]);

    $this->productService
        ->shouldNotReceive('update');

    $this->action->handle($product);

    // Assert no Stripe API call was made (verified by mock expectation)
    expect(true)->toBeTrue();
});
