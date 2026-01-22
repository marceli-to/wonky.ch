<?php

namespace App\Filament\Admin\Resources\Subscribers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Abonnent')
                            ->schema([
                                TextInput::make('email')
                                    ->label('E-Mail')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                TextInput::make('name')
                                    ->label('Name')
                                    ->maxLength(255),
                            ]),
                    ])
                    ->columnSpan(['lg' => 8]),

                Group::make()
                    ->schema([
                        Section::make('Status')
                            ->schema([
                                DateTimePicker::make('subscribed_at')
                                    ->label('Angemeldet am')
                                    ->required()
                                    ->default(now()),

                                DateTimePicker::make('confirmed_at')
                                    ->label('Bestätigt am')
                                    ->helperText('Leer = Double-Opt-In ausstehend'),

                                DateTimePicker::make('unsubscribed_at')
                                    ->label('Abgemeldet am')
                                    ->helperText('Leer lassen für aktive Abonnenten'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 4]),
            ])
            ->columns(12);
    }
}
