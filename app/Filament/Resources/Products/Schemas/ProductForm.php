<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * @codeCoverageIgnore
 */
final class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make()
                    ->schema([
                        TextInput::make('stripe_product_id')
                            ->label('Stripe Product ID')
                            ->disabled()
                            ->placeholder('Will be set after syncing to Stripe')
                            ->visibleOn('edit'),
                        Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
