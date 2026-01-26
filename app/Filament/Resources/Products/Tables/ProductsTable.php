<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\Tables;

use App\Actions\SyncProductToStripe;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * @codeCoverageIgnore
 */
final class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stripe_product_id')
                    ->label('Stripe ID')
                    ->searchable()
                    ->copyable()
                    ->placeholder('Not synced'),
                TextColumn::make('prices_count')
                    ->label('Prices')
                    ->counts('prices'),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('sync')
                    ->label('Sync to Stripe')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Product $record, Action $action, SyncProductToStripe $syncProductToStripe): void {
                        $syncProductToStripe->handle($record);
                        Notification::make()
                            ->title('Product synced to Stripe')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
