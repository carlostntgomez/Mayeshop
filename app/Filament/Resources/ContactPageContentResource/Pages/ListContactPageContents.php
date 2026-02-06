<?php

namespace App\Filament\Resources\ContactPageContentResource\Pages;

use App\Filament\Resources\ContactPageContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactPageContents extends ListRecords
{
    protected static string $resource = ContactPageContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
