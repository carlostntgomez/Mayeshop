<?php

namespace App\Filament\Resources\OccasionTagResource\Pages;

use App\Filament\Resources\OccasionTagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOccasionTag extends EditRecord
{
    protected static string $resource = OccasionTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
