<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogTagResource\Pages;
use App\Filament\Resources\BlogTagResource\RelationManagers;
use App\Models\BlogTag;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlogTagResource extends Resource
{
    protected static ?string $model = BlogTag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Etiquetas';

    protected static bool $isModal = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles de la Etiqueta')
                    ->description('Información básica y SEO para la etiqueta del blog.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null)
                            ->hintAction(
                                Action::make('name_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Introduce el nombre de la etiqueta para el blog. Este nombre será visible para los visitantes y se usa para agrupar publicaciones con temas similares (ej: Marketing, SEO, Diseño).')
                            ),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->hintAction(
                                Action::make('slug_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('El slug es la parte de la URL que identifica a la etiqueta del blog. Se genera automáticamente, pero puedes ajustarlo.')
                            ),
                        TextInput::make('meta_title')
                            ->label('Meta Título')
                            ->maxLength(255)
                            ->hintAction(
                                Action::make('meta_title_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Título para SEO que aparecerá en los resultados de búsqueda y en la pestaña del navegador.')
                            ),
                        Textarea::make('meta_description')
                            ->label('Meta Descripción')
                            ->rows(3)
                            ->maxLength(255)
                            ->hintAction(
                                Action::make('meta_description_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Breve descripción para SEO que aparecerá en los resultados de búsqueda.')
                            ),
                        TextInput::make('meta_keywords')
                            ->label('Meta Palabras Clave')
                            ->maxLength(255)
                            ->hintAction(
                                Action::make('meta_keywords_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Palabras clave relevantes para SEO, separadas por comas.')
                            ),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modal(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBlogTags::route('/'),
        ];
    }
}
