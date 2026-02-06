<?php

namespace App\Filament\Resources\HeaderAnnouncementResource\Pages;

use App\Filament\Resources\HeaderAnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHeaderAnnouncements extends ListRecords
{
    protected static string $resource = HeaderAnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
