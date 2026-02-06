<?php

namespace App\Filament\Resources\WhatsAppSubscriptionResource\Pages;

use App\Filament\Resources\WhatsAppSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWhatsAppSubscription extends EditRecord
{
    protected static string $resource = WhatsAppSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
