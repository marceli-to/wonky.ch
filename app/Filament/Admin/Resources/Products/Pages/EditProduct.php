<?php

namespace App\Filament\Admin\Resources\Products\Pages;

use App\Filament\Admin\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Speichern')
                ->action('save')
                ->color('primary'),
            Action::make('saveAndContinue')
                ->label('Speichern & Weiter')
                ->action(function () {
                    $this->save(shouldRedirect: false);
                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->getRecord()]));
                })
                ->color('gray'),
            Action::make('cancel')
                ->label('Abbrechen')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
