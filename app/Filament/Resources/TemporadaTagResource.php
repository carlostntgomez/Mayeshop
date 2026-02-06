<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemporadaTagResource\Pages;
use App\Filament\Resources\TemporadaTagResource\RelationManagers;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemporadaTagResource extends BaseTagResource
{
    protected static ?string $navigationIcon = 'heroicon-o-sun';

        protected static ?string $navigationGroup = 'Catálogo';

        protected static ?int $navigationSort = 10;

    public static function getType(): string
    {
        return 'temporada';
    }

    public static function getNavigationLabel(): string
    {
        return 'Etiquetas de Temporada';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemporadaTags::route('/'),
        ];
    }
}
