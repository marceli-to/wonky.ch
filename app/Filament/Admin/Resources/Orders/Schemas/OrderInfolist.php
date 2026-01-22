<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Bestellung')
                    ->icon('heroicon-o-document-text')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('order_number')
                            ->label('Bestellnr.')
                            ->weight('bold')
                            ->size('lg'),
                        TextEntry::make('created_at')
                            ->label('Bestellt am')
                            ->dateTime('d.m.Y H:i'),
                        TextEntry::make('paid_at')
                            ->label('Bezahlt am')
                            ->dateTime('d.m.Y H:i')
                            ->placeholder('–'),
                    ]),

                Section::make('Artikel')
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->hiddenLabel()
                            ->columns(4)
                            ->schema([
                                TextEntry::make('product_name')
                                    ->label('Produkt'),
                                TextEntry::make('product_description')
                                    ->label('Beschreibung')
                                    ->placeholder('–'),
                                TextEntry::make('quantity')
                                    ->label('Anzahl'),
                                TextEntry::make('product_price')
                                    ->label('Preis')
                                    ->formatStateUsing(fn ($state) => 'CHF '.number_format($state, 2, '.', '\'')),
                            ]),
                    ]),

                Section::make('Zahlung')
                    ->icon('heroicon-o-credit-card')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('payment_method')
                            ->label('Zahlungsmethode')
                            ->formatStateUsing(fn ($state) => $state === 'invoice' ? 'Rechnung' : 'Kreditkarte')
                            ->badge()
                            ->color(fn ($state) => $state === 'invoice' ? 'warning' : 'info'),
                        TextEntry::make('subtotal')
                            ->label('Zwischensumme')
                            ->formatStateUsing(fn ($state) => 'CHF '.number_format($state, 2, '.', '\'')),
                        TextEntry::make('shipping')
                            ->label('Versand')
                            ->formatStateUsing(fn ($state) => 'CHF '.number_format($state, 2, '.', '\'')),
                        TextEntry::make('tax')
                            ->label('MwSt.')
                            ->formatStateUsing(fn ($state) => 'CHF '.number_format($state, 2, '.', '\'')),
                        TextEntry::make('total')
                            ->label('Total')
                            ->formatStateUsing(fn ($state) => 'CHF '.number_format($state, 2, '.', '\''))
                            ->weight('bold')
                            ->size('lg'),
                    ]),

                Section::make('Rechnungsadresse')
                    ->icon('heroicon-o-document-duplicate')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('invoice_name')
                            ->label('Name'),
                        TextEntry::make('invoice_email')
                            ->label('E-Mail')
                            ->icon('heroicon-o-envelope'),
                        TextEntry::make('invoice_address')
                            ->label('Strasse'),
                        TextEntry::make('invoice_phone')
                            ->label('Telefon')
                            ->icon('heroicon-o-phone')
                            ->placeholder('–'),
                        TextEntry::make('invoice_location')
                            ->label('Ort'),
                        TextEntry::make('invoice_country')
                            ->label('Land'),
                    ]),

                Section::make('Lieferadresse')
                    ->icon('heroicon-o-truck')
                    ->columns(2)
                    ->hidden(fn ($record) => $record->use_invoice_address)
                    ->schema([
                        TextEntry::make('shipping_name')
                            ->label('Name'),
                        TextEntry::make('shipping_address')
                            ->label('Strasse'),
                        TextEntry::make('shipping_location')
                            ->label('Ort'),
                        TextEntry::make('shipping_country')
                            ->label('Land')
                            ->placeholder('–'),
                    ]),
            ]);
    }
}
