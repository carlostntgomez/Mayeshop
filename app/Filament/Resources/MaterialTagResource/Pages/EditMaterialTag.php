<?php

namespace App\Filament\Resources\MaterialTagResource\Pages;

use App\Filament\Resources\MaterialTagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaterialTag extends EditRecord
{
    protected static string $resource = MaterialTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
