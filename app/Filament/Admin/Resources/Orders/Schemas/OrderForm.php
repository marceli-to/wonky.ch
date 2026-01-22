<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->required(),
                TextInput::make('invoice_salutation')
                    ->label('Anrede'),
                TextInput::make('invoice_firstname')
                    ->label('Vorname')
                    ->required(),
                TextInput::make('invoice_lastname')
                    ->label('Nachname')
                    ->required(),
                TextInput::make('invoice_street')
                    ->label('Strasse')
                    ->required(),
                TextInput::make('invoice_street_number')
                    ->label('Nr.'),
                TextInput::make('invoice_zip')
                    ->label('PLZ')
                    ->required(),
                TextInput::make('invoice_city')
                    ->label('Ort')
                    ->required(),
                TextInput::make('invoice_country')
                    ->label('Land')
                    ->required()
                    ->default('Schweiz'),
                TextInput::make('invoice_email')
                    ->label('E-Mail')
                    ->email()
                    ->required(),
                TextInput::make('invoice_phone')
                    ->label('Telefon')
                    ->tel(),
                Toggle::make('use_invoice_address')
                    ->label('Lieferadresse = Rechnungsadresse')
                    ->required(),
                TextInput::make('shipping_salutation')
                    ->label('Anrede (Lieferung)'),
                TextInput::make('shipping_firstname')
                    ->label('Vorname (Lieferung)'),
                TextInput::make('shipping_lastname')
                    ->label('Nachname (Lieferung)'),
                TextInput::make('shipping_street')
                    ->label('Strasse (Lieferung)'),
                TextInput::make('shipping_street_number')
                    ->label('Nr. (Lieferung)'),
                TextInput::make('shipping_zip')
                    ->label('PLZ (Lieferung)'),
                TextInput::make('shipping_city')
                    ->label('Ort (Lieferung)'),
                TextInput::make('shipping_country')
                    ->label('Land (Lieferung)'),
                TextInput::make('total')
                    ->label('Total')
                    ->required()
                    ->numeric(),
                TextInput::make('payment_method')
                    ->label('Zahlungsmethode'),
                TextInput::make('payment_reference')
                    ->label('Zahlungsreferenz'),
                DateTimePicker::make('paid_at')
                    ->label('Bezahlt am'),
            ]);
    }
}
