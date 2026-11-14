<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\UpsertPriceFromStripe;
use App\Actions\UpsertProductFromStripe;
use App\Data\StripePriceData;
use App\Data\StripeProductData;
use Illuminate\Console\Command;
use Stripe\StripeClient;
use Throwable;

final class SyncStripeCatalog extends Command
{
    /**
     * @var string
     */
    protected $signature = 'billing:sync-stripe-catalog
        {--limit=100 : Number of products to fetch per page (1-100)}
        {--starting-after= : Stripe product ID cursor}
        {--only-products : Sync only products}
        {--only-prices : Sync only prices (products still upsert to ensure association)}
        {--dry-run : Fetch and log without persisting changes}';

    /**
     * @var string
     */
    protected $description = 'Sync products and prices from Stripe into the local catalog';

    public function handle(
        StripeClient $stripe,
        UpsertProductFromStripe $upsertProductFromStripe,
        UpsertPriceFromStripe $upsertPriceFromStripe,
    ): int {
        $limit = (int) $this->option('limit');
        if ($limit < 1 || $limit > 100) {
            $this->warn('Limit must be between 1 and 100. Using 100.');
            $limit = 100;
        }

        $cursor = $this->option('starting-after') ?: null;
        $onlyProducts = (bool) $this->option('only-products');
        $onlyPrices = (bool) $this->option('only-prices');
        $dryRun = (bool) $this->option('dry-run');

        $productsSynced = 0;
        $pricesSynced = 0;

        try {
            do {
                $response = $stripe->products->all([
                    'limit' => $limit,
                    'starting_after' => $cursor,
                    'expand' => ['data.prices'],
                ]);

                $products = $response->data ?? [];
                if ($products === []) {
                    break;
                }

                foreach ($products as $stripeProduct) {
                    $productData = $this->mapProduct($stripeProduct);
                    $productsSynced++;

                    $productModel = null;
                    if (! $dryRun) {
                        $productModel = $upsertProductFromStripe->handle($productData);
                    }

                    if (! $onlyProducts) {
                        $prices = $this->extractPrices($stripe, $stripeProduct);

                        foreach ($prices as $stripePrice) {
                            $priceData = $this->mapPrice($stripePrice);
                            if ($priceData === null) {
                                continue;
                            }

                            $pricesSynced++;

                            if ($dryRun) {
                                continue;
                            }

                            $productModel ??= $upsertProductFromStripe->handle($productData);
                            $upsertPriceFromStripe->handle($priceData, $productModel);
                        }
                    }

                    $cursor = $stripeProduct->id;
                }
            } while ($response->has_more ?? false);
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Products synced: {$productsSynced}");
        if (! $onlyProducts) {
            $this->info("Prices synced: {$pricesSynced}");
        }

        if ($dryRun) {
            $this->comment('Dry run mode: no changes were persisted.');
        }

        if ($onlyPrices) {
            $this->comment('Prices were synced; products were still ensured for association.');
        }

        return self::SUCCESS;
    }

    private function mapProduct(object $stripeProduct): StripeProductData
    {
        return new StripeProductData(
            $stripeProduct->id,
            $stripeProduct->name,
            $stripeProduct->description ?? null,
            (bool) ($stripeProduct->active ?? true),
        );
    }

    private function mapPrice(object $stripePrice): ?StripePriceData
    {
        if ($stripePrice->unit_amount === null) {
            $this->warn("Skipping price {$stripePrice->id} because unit_amount is null.");

            return null;
        }

        $interval = $stripePrice->recurring->interval ?? 'one_time';

        return new StripePriceData(
            $stripePrice->id,
            $stripePrice->product,
            (int) $stripePrice->unit_amount,
            $stripePrice->currency,
            $interval,
            (bool) ($stripePrice->active ?? true),
        );
    }

    /**
     * @return array<int, object>
     */
    private function extractPrices(StripeClient $stripe, object $stripeProduct): array
    {
        if (isset($stripeProduct->prices) && isset($stripeProduct->prices->data)) {
            /** @var array<int, object> $data */
            $data = $stripeProduct->prices->data;

            return $data;
        }

        $response = $stripe->prices->all([
            'product' => $stripeProduct->id,
            'limit' => 100,
        ]);

        /** @var array<int, object> $data */
        $data = $response->data ?? [];

        return $data;
    }
}
