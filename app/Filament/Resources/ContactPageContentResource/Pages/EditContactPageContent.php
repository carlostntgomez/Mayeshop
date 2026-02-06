<?php

namespace App\Filament\Resources\ContactPageContentResource\Pages;

use App\Filament\Resources\ContactPageContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactPageContent extends EditRecord
{
    protected static string $resource = ContactPageContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
