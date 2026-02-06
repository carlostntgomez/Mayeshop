<?php

namespace App\Filament\Resources\EstiloTagResource\Pages;

use App\Filament\Resources\EstiloTagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstiloTags extends ListRecords
{
    protected static string $resource = EstiloTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
