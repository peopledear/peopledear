<?php

declare(strict_types=1);

use App\Actions\Product\DeactivateProductInStripe;
use App\Actions\Product\DeleteProduct;
use App\Models\Product;
use Stripe\Service\ProductService;
use Stripe\StripeClient;

beforeEach(function (): void {
    $this->stripeClient = Mockery::mock(StripeClient::class);
    $this->productService = Mockery::mock(ProductService::class);
    $this->stripeClient->products = $this->productService;

    $deactivateProductInStripe = new DeactivateProductInStripe($this->stripeClient);
    $this->action = new DeleteProduct($deactivateProductInStripe);
});

test('deactivates in stripe and deletes product locally', function (): void {
    $product = Product::factory()->create([
        'stripe_product_id' => 'prod_test123',
    ]);

    $this->productService
        ->shouldReceive('update')
        ->once()
        ->with('prod_test123', ['active' => false])
        ->andReturn((object) ['id' => 'prod_test123', 'active' => false]);

    $this->action->handle($product);

    expect(Product::query()->find($product->id))->toBeNull();
});

test('deletes product without stripe_product_id', function (): void {
    $product = Product::factory()->create([
        'stripe_product_id' => null,
    ]);

    $this->productService
        ->shouldNotReceive('update');

    $this->action->handle($product);

    expect(Product::query()->find($product->id))->toBeNull();
});
