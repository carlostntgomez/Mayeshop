<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $label = 'Reseña';

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Catálogo';

    protected static ?string $navigationLabel = 'Reseñas';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('product_id_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Selecciona el producto al que se refiere esta reseña. Esto es importante para que la reseña aparezca en la página correcta del producto.')
                    ),
                Forms\Components\TextInput::make('reviewer_name')
                    ->label('Nombre del Revisor')
                    ->required()
                    ->maxLength(255)
                    ->hintAction(
                        Forms\Components\Actions\Action::make('reviewer_name_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Introduce el nombre de la persona que escribió la reseña. Este nombre será visible públicamente.')
                    ),
                Forms\Components\TextInput::make('reviewer_email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->maxLength(255)
                    ->nullable()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('reviewer_email_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Introduce el correo electrónico del revisor. Este campo es opcional y no será visible públicamente.')
                    ),
                Forms\Components\TextInput::make('reviewer_phone')
                    ->label('Teléfono (WhatsApp)')
                    ->tel()
                    ->maxLength(255)
                    ->nullable()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('reviewer_phone_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Introduce el número de teléfono del revisor. Este campo es opcional y puede ser usado para contactar al cliente por WhatsApp para verificar la reseña.')
                    ),
                Forms\Components\TextInput::make('rating')
                    ->label('Calificación (1-5)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->required()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('rating_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Selecciona la calificación que el cliente le dio al producto, en una escala de 1 a 5 estrellas.')
                    ),
                Forms\Components\Textarea::make('review_text')
                    ->label('Texto de la Reseña')
                    ->required()
                    ->columnSpanFull()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('review_text_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Pega o escribe el texto completo de la reseña del cliente. Asegúrate de que el contenido sea apropiado antes de aprobarlo.')
                    ),
                Forms\Components\Toggle::make('is_approved')
                    ->label('Aprobada')
                    ->hintAction(
                        Forms\Components\Actions\Action::make('is_approved_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Marca esta opción para que la reseña sea visible en la página del producto. Las reseñas deben ser aprobadas manually para asegurar su calidad y veracidad.')
                    )
                    ->default(false),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Destacada en Inicio')
                    ->hintAction(
                        Forms\Components\Actions\Action::make('is_featured_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Marca esta opción para destacar esta reseña en la página de inicio o en otras secciones de testimonios. Ideal para las reseñas más positivas e impactantes.')
                    )
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reviewer_name')
                    ->label('Revisor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Vestido')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Calificación')
                    ->sortable(),
                Tables\Columns\TextColumn::make('review_text')
                    ->label('Reseña')
                    ->limit(50)
                    ->tooltip(fn (?string $state): ?string => strlen($state ?? '') > 50 ? $state : null),
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Aprobada')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacada')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Vestido')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Estado de Aprobación')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListReviews::route('/'),
        ];
    }
}
