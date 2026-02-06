<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialMediaLinkResource\Pages;
use App\Models\SocialMediaLink;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SocialMediaLinkResource extends Resource
{
    protected static ?string $model = SocialMediaLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-share';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Enlaces Redes Sociales';

    protected static ?int $navigationSort = 101;

    protected static bool $isModal = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('name')
                    ->label('Red Social')
                    ->options([
                        'Facebook' => 'Facebook',
                        'Twitter' => 'Twitter',
                        'Instagram' => 'Instagram',
                        'YouTube' => 'YouTube',
                        'TikTok' => 'TikTok',
                        'Pinterest' => 'Pinterest',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $icon = match ($state) {
                            'Facebook' => 'fab fa-facebook-f',
                            'Twitter' => 'fab fa-twitter',
                            'Instagram' => 'fab fa-instagram',
                            'YouTube' => 'fab fa-youtube',
                            'TikTok' => 'fab fa-tiktok',
                            'Pinterest' => 'fab fa-pinterest',
                            default => null,
                        };
                        $set('icon', $icon);
                    })
                    ->hintAction(
                        Action::make('name_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Selecciona la red social para la que deseas añadir el enlace.')
                    ),
                Hidden::make('icon'), // Hidden field, value set by 'name' field
                TextInput::make('url')
                    ->label('Enlace (URL)')
                    ->url()
                    ->required()
                    ->hintAction(
                        Action::make('url_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Introduce la URL completa de tu perfil en la red social seleccionada.')
                    ),
                TextInput::make('order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->hintAction(
                        Action::make('order_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Define el orden en que aparecerán los enlaces de redes sociales. Los números más bajos se muestran primero.')
                    ),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true)
                    ->hintAction(
                        Action::make('is_active_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Activa o desactiva la visibilidad de este enlace de red social en el sitio web.')
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Red Social')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->label('Enlace')
                    ->url(fn (string $state): string => $state)
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListSocialMediaLinks::route('/'),
        ];
    }
}