<?php

namespace App\Filament\Admin\Resources\Subscribers;

use App\Filament\Admin\Resources\Subscribers\Pages\CreateSubscriber;
use App\Filament\Admin\Resources\Subscribers\Pages\EditSubscriber;
use App\Filament\Admin\Resources\Subscribers\Pages\ListSubscribers;
use App\Filament\Admin\Resources\Subscribers\Schemas\SubscriberForm;
use App\Filament\Admin\Resources\Subscribers\Tables\SubscribersTable;
use App\Models\Subscriber;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Abonnenten';

    protected static ?string $modelLabel = 'Abonnent';

    protected static ?string $pluralModelLabel = 'Abonnenten';

    protected static ?string $breadcrumb = 'Abonnenten';

    protected static string|UnitEnum|null $navigationGroup = 'Newsletter';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return SubscriberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubscribersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscribers::route('/'),
            'create' => CreateSubscriber::route('/create'),
            'edit' => EditSubscriber::route('/{record}/edit'),
        ];
    }
}
