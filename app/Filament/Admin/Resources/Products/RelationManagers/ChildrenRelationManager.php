<?php

namespace App\Filament\Admin\Resources\Products\RelationManagers;

use App\Enums\ProductType;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    protected static ?string $title = 'Produktvarianten';

    protected static ?string $modelLabel = 'Variante';

    protected static ?string $pluralModelLabel = 'Varianten';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type === ProductType::Variations;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('type')
                    ->default('simple'),

                Hidden::make('published')
                    ->default(true),

                TextInput::make('label')
                    ->label('Label')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('sku')
                    ->label('SKU')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('short_description')
                    ->label('Kurzbeschreibung')
                    ->rows(2)
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->label('Preis')
                    ->numeric()
                    ->prefix('CHF')
                    ->required()
                    ->default(0)
                    ->columnSpanFull(),

                TextInput::make('stock')
                    ->label('Lagerbestand')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Preis')
                    ->formatStateUsing(fn ($state) => 'CHF '.number_format($state, 2, '.', '\''))
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Lagerbestand')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Variante erstellen')
                    ->modalWidth('lg')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Child products inherit name from parent
                        $data['name'] = $this->ownerRecord->name;
                        $data['slug'] = \Illuminate\Support\Str::slug($this->ownerRecord->name.'-'.$data['label']).'-'.\Illuminate\Support\Str::random(6);

                        return $data;
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Bearbeiten')
                        ->modalWidth('lg'),
                    DeleteAction::make()
                        ->label('Löschen'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
