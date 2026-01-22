<?php

namespace App\Filament\Admin\Resources\Newsletters\Schemas;

use App\Models\Newsletter;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsletterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Newsletter')
                            ->schema([
                                TextInput::make('subject')
                                    ->label('Betreff')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('preheader')
                                    ->label('Preheader')
                                    ->maxLength(255)
                                    ->helperText('Vorschautext in der E-Mail-Liste (optional)'),
                            ]),

                        Section::make('Artikel')
                            ->schema([
                                Repeater::make('articles')
                                    ->label('Artikel')
                                    ->relationship('articles')
                                    ->orderColumn('order')
                                    ->reorderable()
                                    ->collapsible()
                                    ->collapsed()
                                    ->addActionLabel('Artikel hinzufügen')
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Neuer Artikel')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Titel')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Textarea::make('text')
                                            ->label('Text')
                                            ->required()
                                            ->rows(6)
                                            ->columnSpanFull(),

                                        Repeater::make('images')
                                            ->label('Bilder')
                                            ->relationship('images')
                                            ->orderColumn('order')
                                            ->reorderable()
                                            ->collapsible()
                                            ->collapsed()
                                            ->addActionLabel('Bild hinzufügen')
                                            ->itemLabel(fn (array $state): ?string => $state['caption'] ?? 'Bild')
                                            ->schema([
                                                FileUpload::make('file_path')
                                                    ->label('Bild')
                                                    ->image()
                                                    ->directory('newsletter')
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
                                            ])
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                $data['file_name'] = basename($data['file_path']);

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
                                            ->columns(1)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpan(['lg' => 8]),

                Group::make()
                    ->schema([
                        Section::make('Info')
                            ->schema([
                                Placeholder::make('status_display')
                                    ->label('Status')
                                    ->content(function (?Newsletter $record): string {
                                        if (!$record) {
                                            return 'Entwurf';
                                        }
                                        if ($record->isSending()) {
                                            return "Senden [{$record->progress}]";
                                        }
                                        return $record->isSent() ? 'Gesendet' : 'Entwurf';
                                    }),

                                Placeholder::make('sent_at_display')
                                    ->label('Gesendet am')
                                    ->content(fn (?Newsletter $record): string => $record?->sent_at?->format('d.m.Y H:i') ?? '—')
                                    ->visible(fn (?Newsletter $record): bool => $record?->isSent() ?? false),
                            ]),
                    ])
                    ->columnSpan(['lg' => 4]),
            ])
            ->columns(12);
    }
}
