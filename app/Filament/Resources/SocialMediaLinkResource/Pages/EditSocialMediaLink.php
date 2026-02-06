<?php

namespace App\Filament\Resources\SocialMediaLinkResource\Pages;

use App\Filament\Resources\SocialMediaLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSocialMediaLink extends EditRecord
{
    protected static string $resource = SocialMediaLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
