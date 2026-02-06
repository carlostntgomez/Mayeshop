<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactPageContentResource\Pages;
use App\Filament\Resources\ContactPageContentResource\RelationManagers;
use App\Models\ContactPageContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactPageContentResource extends Resource
{
    protected static ?string $model = ContactPageContent::class;

    protected static ?string $navigationGroup = 'Páginas';
    protected static ?string $navigationLabel = 'Contacto';
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('breadcrumb_title')
                    ->label('Título del Breadcrumb')
                    ->required()
                    ->maxLength(255)
                    ->tooltip('Título que aparece en la barra de navegación o encabezado de la página de contacto.'),
                Forms\Components\TextInput::make('heading_title')
                    ->label('Título del Encabezado')
                    ->required()
                    ->maxLength(255)
                    ->tooltip('Título principal de la sección de contacto, por ejemplo, "Ponte en Contacto".'),
                Forms\Components\Textarea::make('heading_description')
                    ->label('Descripción del Encabezado')
                    ->required()
                    ->rows(3)
                    ->tooltip('Descripción que acompaña al título principal de la sección de contacto.'),
                Forms\Components\TextInput::make('address')
                    ->label('Dirección')
                    ->required()
                    ->maxLength(255)
                    ->tooltip('Dirección física de la empresa o punto de contacto.'),
                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono')
                    ->required()
                    ->maxLength(255)
                    ->tooltip('Número de teléfono de contacto de la empresa.'),
                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->required()
                    ->email()
                    ->maxLength(255)
                    ->tooltip('Dirección de correo electrónico de contacto de la empresa.'),
                Forms\Components\Textarea::make('map_embed_code')
                    ->label('Código de Inserción del Mapa (iframe)')
                    ->nullable()
                    ->rows(5)
                    ->tooltip('Pega aquí el código iframe de Google Maps para mostrar un mapa en la página de contacto.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('breadcrumb_title')
                    ->label('Título del Breadcrumb')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('heading_title')
                    ->label('Título del Encabezado')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modal(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactPageContents::route('/'),
            'create' => Pages\CreateContactPageContent::route('/create'),
            'edit' => Pages\EditContactPageContent::route('/{record}/edit'),
        ];
    }
}
