<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Bestellnr.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_name')
                    ->label('Name')
                    ->searchable(['invoice_firstname', 'invoice_lastname'])
                    ->sortable(['invoice_lastname', 'invoice_firstname']),
                TextColumn::make('invoice_email')
                    ->label('E-Mail')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => number_format($state, 2, '.', '\''))
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Zahlungsmethode')
                    ->formatStateUsing(fn ($state) => $state === 'invoice' ? 'Rechnung' : 'Kreditkarte')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Datum')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ansehen'),
                    EditAction::make()
                        ->label('Bearbeiten'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
