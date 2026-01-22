<?php

namespace App\Filament\Admin\Resources\Newsletters\Pages;

use App\Filament\Admin\Resources\Newsletters\NewsletterResource;
use App\Jobs\SendNewsletterJob;
use App\Mail\NewsletterMail;
use App\Models\Subscriber;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditNewsletter extends EditRecord
{
    protected static string $resource = NewsletterResource::class;

    protected function getHeaderActions(): array
    {
        $newsletter = $this->getRecord();
        $subscriberCount = Subscriber::whereNotNull('confirmed_at')
            ->whereNull('unsubscribed_at')
            ->count();

        return [
            Action::make('sendNewsletter')
                ->label('Newsletter senden')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->visible(fn () => !$newsletter->isSent())
                ->requiresConfirmation()
                ->modalHeading('Newsletter senden')
                ->modalDescription("Der Newsletter wird an {$subscriberCount} Abonnenten gesendet. Dieser Vorgang kann nicht rückgängig gemacht werden.")
                ->modalSubmitActionLabel('Jetzt senden')
                ->action(function () use ($newsletter) {
                    $subscribers = Subscriber::whereNotNull('confirmed_at')
                        ->whereNull('unsubscribed_at')
                        ->get();

                    $newsletter->update([
                        'recipients_count' => $subscribers->count(),
                        'sent_count' => 0,
                        'sent_at' => now(),
                    ]);

                    foreach ($subscribers as $subscriber) {
                        SendNewsletterJob::dispatch($newsletter, $subscriber);
                    }

                    Notification::make()
                        ->title('Newsletter wird gesendet')
                        ->body("Der Newsletter wird an {$subscribers->count()} Abonnenten gesendet.")
                        ->success()
                        ->send();
                }),

            Action::make('sendPreview')
                ->label('Vorschau senden')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->form([
                    TextInput::make('preview_email')
                        ->label('E-Mail Adresse')
                        ->email()
                        ->required()
                        ->default(fn () => auth()->user()?->email),
                ])
                ->action(function (array $data) {
                    $newsletter = $this->getRecord();

                    Mail::to($data['preview_email'])
                        ->send(new NewsletterMail($newsletter, isPreview: true));

                    Notification::make()
                        ->title('Vorschau gesendet')
                        ->body('Die Newsletter-Vorschau wurde an '.$data['preview_email'].' gesendet.')
                        ->success()
                        ->send();
                }),

            DeleteAction::make()
                ->label('Löschen'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
