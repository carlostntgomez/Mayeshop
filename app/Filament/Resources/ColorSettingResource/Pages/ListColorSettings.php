<?php

namespace App\Filament\Resources\ColorSettingResource\Pages;

use App\Filament\Resources\ColorSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListColorSettings extends ListRecords
{
    protected static string $resource = ColorSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modal(),
        ];
    }
}
