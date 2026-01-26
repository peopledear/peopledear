<?php

declare(strict_types=1);

namespace App\Filament\Resources\Prices;

use App\Filament\Resources\Prices\Pages\CreatePrice;
use App\Filament\Resources\Prices\Pages\EditPrice;
use App\Filament\Resources\Prices\Pages\ListPrices;
use App\Filament\Resources\Prices\Schemas\PriceForm;
use App\Filament\Resources\Prices\Tables\PricesTable;
use App\Models\Price;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * @codeCoverageIgnore
 */
final class PriceResource extends Resource
{
    protected static ?string $model = Price::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyEuro;

    protected static string|UnitEnum|null $navigationGroup = 'Subscriptions';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PriceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PricesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrices::route('/'),
            'create' => CreatePrice::route('/create'),
            'edit' => EditPrice::route('/{record}/edit'),
        ];
    }
}
