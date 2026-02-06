<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeaderAnnouncementResource\Pages;
use App\Filament\Resources\HeaderAnnouncementResource\RelationManagers;
use App\Models\HeaderAnnouncement;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HeaderAnnouncementResource extends Resource
{
    protected static ?string $model = HeaderAnnouncement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Home';

    protected static ?string $navigationLabel = 'Anuncios de Cabecera';

    protected static ?int $navigationSort = 10;

    protected static bool $isModal = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('text')
                    ->label('Texto del Anuncio')
                    ->required()
                    ->maxLength(255)
                    ->hintAction(
                        Action::make('text_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Introduce el texto que se mostrará como anuncio en la cabecera. Es ideal para mensajes cortos y promociones.')
                    ),
                TextInput::make('icon')
                    ->label('Clase del Icono (ej: fas fa-star)')
                    ->nullable()
                    ->maxLength(255)
                    ->hintAction(
                        Action::make('icon_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Clase CSS del icono a mostrar junto al texto (ej. de Flaticon o Heroicons). Opcional.')
                 ),
                TextInput::make('url')
                    ->label('URL (opcional)')
                    ->url()
                    ->nullable()
                    ->maxLength(255)
                    ->hintAction(
                        Action::make('url_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('URL a la que el usuario será redirigido al hacer clic en el anuncio. Si se deja vacío, el anuncio no será clicable.')
                 ),
                TextInput::make('order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->hintAction(
                        Action::make('order_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Define el orden de aparición del anuncio en la cabecera. Los números más bajos aparecen primero.')
                 ),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true)
                    ->hintAction(
                        Action::make('is_active_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Activa o desactiva la visibilidad del anuncio en la página web.')
                 ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),
                Tables\Columns\TextColumn::make('text')
                    ->label('Texto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('icon')
                    ->label('Icono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->url(fn (string $state): string => $state)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('order');
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
            'index' => Pages\ListHeaderAnnouncements::route('/'),
        ];
    }
}
