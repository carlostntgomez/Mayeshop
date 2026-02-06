<?php

namespace App\Filament\Resources\HeaderAnnouncementResource\Pages;

use App\Filament\Resources\HeaderAnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHeaderAnnouncement extends EditRecord
{
    protected static string $resource = HeaderAnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
