<?php

namespace App\Filament\Resources\OccasionTagResource\Pages;

use App\Filament\Resources\OccasionTagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOccasionTags extends ListRecords
{
    protected static string $resource = OccasionTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
