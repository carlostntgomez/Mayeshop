<?php

namespace App\Filament\Resources\FeaturedProductsSectionResource\Pages;

use App\Filament\Resources\FeaturedProductsSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeaturedProductsSections extends ListRecords
{
    protected static string $resource = FeaturedProductsSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
