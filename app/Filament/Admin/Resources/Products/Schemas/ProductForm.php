<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use App\Enums\ProductType;
use App\Models\Category;
use App\Models\ShippingMethod;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Produkt')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),

                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->maxLength(255),

                                Textarea::make('short_description')
                                    ->label('Kurzbeschreibung')
                                    ->rows(2)
                                    ->maxLength(255)
                                    ->helperText('Kurze Produktbeschreibung (max. 255 Zeichen)'),

                                Textarea::make('description')
                                    ->label('Beschreibung')
                                    ->rows(4),

                                TextInput::make('delivery_time')
                                    ->label('Lieferzeit')
                                    ->maxLength(255)
                                    ->helperText('z.B. "2-3 Werktage"'),

                                TextInput::make('price')
                                    ->label('Preis')
                                    ->required()
                                    ->numeric()
                                    ->prefix('CHF')
                                    ->step(0.01),

                                TextInput::make('stock')
                                    ->label('Lagerbestand')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ]),
                    ])
                    ->columnSpan(['lg' => 8]),

                Group::make()
                    ->schema([
                        Section::make('Einstellungen')
                            ->schema([
                                Toggle::make('published')
                                    ->label('Publizieren')
                                    ->inline(false)
                                    ->default(false),

                                Select::make('type')
                                    ->label('Typ')
                                    ->options(collect(ProductType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->label()]))
                                    ->default(ProductType::Simple->value)
                                    ->required(),

                                Section::make('Kategorien')
                                    ->schema([
                                        CheckboxList::make('categories')
                                            ->label('Kategorien')
                                            ->relationship('categories', 'name')
                                            ->options(Category::pluck('name', 'id'))
                                            ->columns(2),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),

                                Section::make('Versandarten')
                                    ->schema([
                                        CheckboxList::make('shippingMethods')
                                            ->label('Versandarten')
                                            ->relationship('shippingMethods', 'name')
                                            ->options(ShippingMethod::orderBy('order')->pluck('name', 'id'))
                                            ->columns(1),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),
                            ]),

                        Section::make('Bilder')
                            ->schema([
                                Repeater::make('images')
                                    ->label('Bilder')
                                    ->relationship('images')
                                    ->addActionLabel('Bild hinzufügen')
                                    ->orderColumn('order')
                                    ->reorderable()
                                    ->collapsible()
                                    ->collapsed()
                                    ->itemLabel(fn (array $state): ?string => $state['caption'] ?? 'Bild')
                                    ->schema([
                                        FileUpload::make('file_path')
                                            ->label('Bild')
                                            ->image()
                                            ->directory('products')
                                            ->disk('public')
                                            ->imagePreviewHeight('250')
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                                            ->helperText('Erlaubte Dateitypen: JPG, PNG, WebP')
                                            ->required()
                                            ->columnSpanFull()
                                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                                                $name = $fileName.'-'.uniqid().'.'.$file->extension();

                                                return (string) str($name);
                                            }),

                                        TextInput::make('caption')
                                            ->label('Bildunterschrift')
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Toggle::make('preview')
                                            ->label('Vorschaubild')
                                            ->helperText('Dieses Bild wird in der Kategorieansicht angezeigt')
                                            ->inline(false)
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
                    ->columnSpan(['lg' => 4]),
            ])
            ->columns(12);
    }
}
