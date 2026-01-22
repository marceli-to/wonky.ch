<?php

namespace App\Filament\Admin\Resources\Subscribers\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscribersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('subscribed_at', 'desc')
            ->columns([
                TextColumn::make('email')
                    ->label('E-Mail')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('subscribed_at')
                    ->label('Angemeldet am')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                IconColumn::make('is_confirmed')
                    ->label('Bestätigt')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->confirmed_at !== null)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw("confirmed_at IS NOT NULL $direction");
                    }),

                TextColumn::make('confirmed_at')
                    ->label('Bestätigt am')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('unsubscribed_at')
                    ->label('Abgemeldet am')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('confirmed')
                    ->label('Nur bestätigte')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('confirmed_at')->whereNull('unsubscribed_at'))
                    ->default(),

                Filter::make('pending')
                    ->label('Ausstehend')
                    ->query(fn (Builder $query): Builder => $query->whereNull('confirmed_at')),

                Filter::make('unsubscribed')
                    ->label('Abgemeldet')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('unsubscribed_at')),
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
