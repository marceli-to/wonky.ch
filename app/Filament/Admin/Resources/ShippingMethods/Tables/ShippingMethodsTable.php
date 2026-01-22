<?php

namespace App\Filament\Admin\Resources\ShippingMethods\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShippingMethodsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Preis')
                    ->formatStateUsing(fn ($state) => $state > 0 ? number_format($state, 2, '.', '\'') : '–')
                    ->sortable(),
                TextColumn::make('products_count')
                    ->label('Produkte')
                    ->counts('products')
                    ->sortable(),
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
