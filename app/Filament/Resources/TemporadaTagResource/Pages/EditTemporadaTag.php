<?php

namespace App\Filament\Resources\TemporadaTagResource\Pages;

use App\Filament\Resources\TemporadaTagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemporadaTag extends EditRecord
{
    protected static string $resource = TemporadaTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
