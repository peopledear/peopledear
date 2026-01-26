<?php

declare(strict_types=1);

namespace App\Filament\Resources\Prices\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * @codeCoverageIgnore
 */
final class PriceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('interval')
                            ->options([
                                'month' => 'Monthly',
                                'year' => 'Yearly',
                            ])
                            ->required(),
                        TextInput::make('amount')
                            ->label('Amount (in cents)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Enter amount in cents (e.g., 1000 = €10.00)'),
                        Select::make('currency')
                            ->options([
                                'eur' => 'EUR (€)',
                                'usd' => 'USD ($)',
                                'gbp' => 'GBP (£)',
                            ])
                            ->default('eur')
                            ->required(),
                    ])->columns(2)->columnSpanFull(),
                Section::make()
                    ->schema([
                        TextInput::make('stripe_price_id')
                            ->label('Stripe Price ID')
                            ->disabled()
                            ->placeholder('Will be set after syncing to Stripe')
                            ->visibleOn('edit')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->default(true),
                    ])->columnSpanFull(),
            ]);
    }
}
