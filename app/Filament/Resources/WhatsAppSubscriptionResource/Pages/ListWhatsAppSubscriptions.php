<?php

namespace App\Filament\Resources\WhatsAppSubscriptionResource\Pages;

use App\Filament\Resources\WhatsAppSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWhatsAppSubscriptions extends ListRecords
{
    protected static string $resource = WhatsAppSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
