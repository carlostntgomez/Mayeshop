<?php

namespace App\Filament\Resources\HomepageSubBannerResource\Pages;

use App\Filament\Resources\HomepageSubBannerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomepageSubBanners extends ListRecords
{
    protected static string $resource = HomepageSubBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
