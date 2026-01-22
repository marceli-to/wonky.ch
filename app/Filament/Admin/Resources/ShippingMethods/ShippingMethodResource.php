<?php

namespace App\Filament\Admin\Resources\ShippingMethods;

use App\Filament\Admin\Resources\ShippingMethods\Pages\CreateShippingMethod;
use App\Filament\Admin\Resources\ShippingMethods\Pages\EditShippingMethod;
use App\Filament\Admin\Resources\ShippingMethods\Pages\ListShippingMethods;
use App\Filament\Admin\Resources\ShippingMethods\Schemas\ShippingMethodForm;
use App\Filament\Admin\Resources\ShippingMethods\Tables\ShippingMethodsTable;
use App\Models\ShippingMethod;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ShippingMethodResource extends Resource
{
    protected static ?string $model = ShippingMethod::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Versandarten';

    protected static ?string $modelLabel = 'Versandart';

    protected static ?string $pluralModelLabel = 'Versandarten';

    protected static ?string $breadcrumb = 'Versandarten';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ShippingMethodForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShippingMethodsTable::configure($table);
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
            'index' => ListShippingMethods::route('/'),
            'create' => CreateShippingMethod::route('/create'),
            'edit' => EditShippingMethod::route('/{record}/edit'),
        ];
    }
}
