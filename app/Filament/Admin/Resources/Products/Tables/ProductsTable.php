<?php

namespace App\Filament\Admin\Resources\Products\Tables;

use App\Enums\ProductType;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->whereNull('parent_id'))
            ->columns([
                ImageColumn::make('media.file_path')
                    ->label('Bild')
                    ->disk('public')
                    ->size(40)
                    ->getStateUsing(function ($record) {
                        return $record->images()->orderBy('order')->first()?->file_path;
                    }),
                TextColumn::make('type')
                    ->label('Typ')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => match ($state) {
                        ProductType::Simple => 'gray',
                        ProductType::Variations => 'success',
                    }),
                TextColumn::make('title')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Beschreibung')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('price')
                    ->label('Preis')
                    ->formatStateUsing(fn ($state) => number_format($state, 2, '.', '\''))
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Lagerbestand')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('published')
                    ->label('Publiziert')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Erstellt')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Aktualisiert')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Bearbeiten'),
                    DeleteAction::make()
                        ->label('Löschen'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
