<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OccasionTagResource\Pages;
use App\Filament\Resources\OccasionTagResource\RelationManagers;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OccasionTagResource extends BaseTagResource
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

        protected static ?string $navigationGroup = 'Catálogo';

        protected static ?int $navigationSort = 7;

    public static function getType(): string
    {
        return 'ocasion';
    }

    public static function getNavigationLabel(): string
    {
        return 'Etiquetas de Ocasión';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOccasionTags::route('/'),
        ];
    }
}
