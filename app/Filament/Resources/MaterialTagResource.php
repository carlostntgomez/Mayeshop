<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialTagResource\Pages;
use App\Filament\Resources\MaterialTagResource\RelationManagers;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaterialTagResource extends BaseTagResource
{
    protected static ?string $navigationIcon = 'heroicon-o-cube';

        protected static ?string $navigationGroup = 'Catálogo';

        protected static ?int $navigationSort = 9;

    public static function getType(): string
    {
        return 'material';
    }

    public static function getNavigationLabel(): string
    {
        return 'Etiquetas de Material';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterialTags::route('/'),
        ];
    }
}
