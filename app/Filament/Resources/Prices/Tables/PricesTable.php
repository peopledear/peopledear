<?php

declare(strict_types=1);

namespace App\Filament\Resources\Prices\Tables;

use App\Actions\SyncPriceToStripe;
use App\Models\Price;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Number;

/**
 * @codeCoverageIgnore
 */
final class PricesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('interval')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'month' => 'Monthly',
                        'year' => 'Yearly',
                        default => $state,
                    }),
                TextColumn::make('amount')
                    ->label('Price')
                    ->formatStateUsing(fn (int $state, Price $record): string => Number::currency($state / 100, $record->currency) ?: (string) $state)
                    ->sortable(),
                TextColumn::make('stripe_price_id')
                    ->label('Stripe ID')
                    ->searchable()
                    ->copyable()
                    ->placeholder('Not synced'),
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
                    ->action(function (Price $record, Action $action, SyncPriceToStripe $syncPriceToStripe): void {
                        $syncPriceToStripe->handle($record);
                        Notification::make()
                            ->title('Price synced to Stripe')
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
