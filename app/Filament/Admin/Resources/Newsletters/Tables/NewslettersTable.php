<?php

namespace App\Filament\Admin\Resources\Newsletters\Tables;

use App\Jobs\SendNewsletterJob;
use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\Subscriber;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class NewslettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('subject')
                    ->label('Betreff')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function (Newsletter $record): string {
                        if ($record->isSending()) {
                            return "Senden [{$record->progress}]";
                        }
                        return $record->isSent() ? 'Gesendet' : 'Entwurf';
                    })
                    ->color(function (Newsletter $record): string {
                        if ($record->isSending()) {
                            return 'warning';
                        }
                        return $record->isSent() ? 'success' : 'gray';
                    }),

                TextColumn::make('articles_count')
                    ->label('Artikel')
                    ->counts('articles')
                    ->sortable(),

                TextColumn::make('sent_at')
                    ->label('Gesendet am')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Entwurf',
                        'sent' => 'Gesendet',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value']) {
                            'draft' => $query->whereNull('sent_at'),
                            'sent' => $query->whereNotNull('sent_at'),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('sendNewsletter')
                        ->label('Senden')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('primary')
                        ->visible(fn (Newsletter $record) => !$record->isSent())
                        ->requiresConfirmation()
                        ->modalHeading('Newsletter senden')
                        ->modalDescription(function () {
                            $count = Subscriber::whereNotNull('confirmed_at')
                                ->whereNull('unsubscribed_at')
                                ->count();
                            return "Der Newsletter wird an {$count} Abonnenten gesendet. Dieser Vorgang kann nicht rückgängig gemacht werden.";
                        })
                        ->modalSubmitActionLabel('Jetzt senden')
                        ->action(function (Newsletter $record) {
                            $subscribers = Subscriber::whereNotNull('confirmed_at')
                                ->whereNull('unsubscribed_at')
                                ->get();

                            $record->update([
                                'recipients_count' => $subscribers->count(),
                                'sent_count' => 0,
                                'sent_at' => now(),
                            ]);

                            foreach ($subscribers as $subscriber) {
                                SendNewsletterJob::dispatch($record, $subscriber);
                            }

                            Notification::make()
                                ->title('Newsletter wird gesendet')
                                ->body("Der Newsletter wird an {$subscribers->count()} Abonnenten gesendet.")
                                ->success()
                                ->send();
                        }),

                    Action::make('sendPreview')
                        ->label('Vorschau')
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->modalWidth('sm')
                        ->form([
                            TextInput::make('preview_email')
                                ->label('E-Mail Adresse')
                                ->email()
                                ->required()
                                ->default(fn () => auth()->user()?->email),
                        ])
                        ->action(function (Newsletter $record, array $data) {
                            Mail::to($data['preview_email'])
                                ->send(new NewsletterMail($record, isPreview: true));

                            Notification::make()
                                ->title('Vorschau gesendet')
                                ->body('Die Newsletter-Vorschau wurde an ' . $data['preview_email'] . ' gesendet.')
                                ->success()
                                ->send();
                        }),

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
