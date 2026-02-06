<?php

namespace App\Filament\Resources\MaterialTagResource\Pages;

use App\Filament\Resources\MaterialTagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaterialTags extends ListRecords
{
    protected static string $resource = MaterialTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
