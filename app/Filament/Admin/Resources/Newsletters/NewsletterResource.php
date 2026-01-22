<?php

namespace App\Filament\Admin\Resources\Newsletters;

use App\Filament\Admin\Resources\Newsletters\Pages\CreateNewsletter;
use App\Filament\Admin\Resources\Newsletters\Pages\EditNewsletter;
use App\Filament\Admin\Resources\Newsletters\Pages\ListNewsletters;
use App\Filament\Admin\Resources\Newsletters\Schemas\NewsletterForm;
use App\Filament\Admin\Resources\Newsletters\Tables\NewslettersTable;
use App\Models\Newsletter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Newsletter';

    protected static ?string $modelLabel = 'Newsletter';

    protected static ?string $pluralModelLabel = 'Newsletter';

    protected static ?string $breadcrumb = 'Newsletter';

    protected static string|UnitEnum|null $navigationGroup = 'Newsletter';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return NewsletterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewslettersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsletters::route('/'),
            'create' => CreateNewsletter::route('/create'),
            'edit' => EditNewsletter::route('/{record}/edit'),
        ];
    }
}
