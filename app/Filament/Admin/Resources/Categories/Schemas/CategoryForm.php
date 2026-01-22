<?php

namespace App\Filament\Admin\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([

                        Section::make('Kategorie')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                            ]),

                        Section::make('Bild')
                            ->schema([
                                Repeater::make('image')
                                    ->label('Bild')
                                    ->relationship('image')
                                    ->maxItems(1)
                                    ->addActionLabel('Bild hinzufügen')
                                    ->collapsible()
                                    ->collapsed()
                                    ->itemLabel(fn (array $state): ?string => $state['caption'] ?? 'Bild')
                                    ->schema([
                                        FileUpload::make('file_path')
                                            ->label('Bild')
                                            ->image()
                                            ->directory('categories')
                                            ->disk('public')
                                            ->imagePreviewHeight('250')
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                                            ->helperText('Erlaubte Dateitypen: JPG, PNG, WebP')
                                            ->required()
                                            ->columnSpanFull(),

                                        TextInput::make('caption')
                                            ->label('Bildunterschrift')
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                    ])
                                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                        $data['file_name'] = basename($data['file_path']);

                                        // Get image dimensions
                                        $filePath = storage_path('app/public/'.$data['file_path']);
                                        if (file_exists($filePath)) {
                                            $imageInfo = getimagesize($filePath);
                                            if ($imageInfo !== false) {
                                                $data['width'] = $imageInfo[0];
                                                $data['height'] = $imageInfo[1];
                                            }
                                        }

                                        return $data;
                                    })
                                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                        if (isset($data['file_path'])) {
                                            $data['file_name'] = basename($data['file_path']);

                                            // Get image dimensions if file path changed
                                            $filePath = storage_path('app/public/'.$data['file_path']);
                                            if (file_exists($filePath)) {
                                                $imageInfo = getimagesize($filePath);
                                                if ($imageInfo !== false) {
                                                    $data['width'] = $imageInfo[0];
                                                    $data['height'] = $imageInfo[1];
                                                }
                                            }
                                        }

                                        return $data;
                                    })
                                    ->columns(1),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 8]),

                Section::make('Einstellungen')
                    ->schema([
                        Toggle::make('featured')
                            ->label('Featured')
                            ->inline(false)
                            ->default(true)
                            ->helperText('Kategorie auf der Startseite anzeigen'),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->unique(ignoreRecord: true),
                    ])
                    ->columnSpan(['lg' => 4]),

            ])
            ->columns(12);
    }
}
