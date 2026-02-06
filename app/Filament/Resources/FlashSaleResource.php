<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlashSaleResource\Pages;
use App\Filament\Resources\FlashSaleResource\RelationManagers;
use App\Models\FlashSale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlashSaleResource extends Resource
{
    protected static ?string $model = FlashSale::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = 'Ventas';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Ventas Flash';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('name_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Introduce un nombre para la venta flash. Este nombre es para tu referencia interna (ej: "Venta de Verano 2025").')
                    ),
                Forms\Components\DateTimePicker::make('start_date')
                    ->label('Fecha de Inicio')
                    ->required()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('start_date_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Establece la fecha y hora en que comenzará la venta flash. Los productos seleccionados mostrarán el precio de oferta a partir de este momento.')
                    ),
                Forms\Components\DateTimePicker::make('end_date')
                    ->label('Fecha de Fin')
                    ->required()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('end_date_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Establece la fecha y hora en que terminará la venta flash. Después de este momento, los productos volverán a su precio original.')
                    ),
                Forms\Components\Toggle::make('is_active')
                    ->label('Activa')
                    ->hintAction(
                        Forms\Components\Actions\Action::make('is_active_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Activa esta venta flash. Solo puede haber una venta flash activa a la vez. Si activas esta, cualquier otra venta flash activa se desactivará automáticamente.')
                    )
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state, $record) {
                        if ($state) {
                            FlashSale::where('id', '!=', $record->id ?? null)->update(['is_active' => false]);
                            Notification::make()
                                ->title('Venta Flash Activada')
                                ->body('Esta venta flash ha sido activada. Las demás ventas flash han sido desactivadas.')
                                ->success()
                                ->send();
                        }
                    }),

                Forms\Components\Select::make('products')
                    ->relationship('products', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label('Productos en Venta Flash')
                    ->hintAction(
                        Forms\Components\Actions\Action::make('products_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Selecciona todos los productos que quieres incluir en esta venta flash. Los productos seleccionados mostrarán su "Precio de Oferta" mientras la venta flash esté activa.')
                    )
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Fecha de Inicio')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fecha de Fin')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('N Productos'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListFlashSales::route('/'),
        ];
    }
}
