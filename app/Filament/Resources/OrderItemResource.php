<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Filament\Resources\OrderItemResource\RelationManagers;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationLabel = 'Items de Pedido';
    protected static ?int $navigationSort = 2;

    protected static bool $isModal = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('order_id'), // This will be set by the parent OrderResource
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label('Producto')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->hintAction(
                        Action::make('product_id_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Selecciona el producto asociado a este artículo del pedido.')
                    ),
                TextInput::make('product_name')
                    ->label('Nombre del Producto')
                    ->required()
                    ->maxLength(255)
                    ->hintAction(
                        Action::make('product_name_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Nombre del producto en el momento de la compra. Se rellena automáticamente al seleccionar un producto.')
                    ),
                TextInput::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->hintAction(
                        Action::make('quantity_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Cantidad de este producto incluida en el pedido.')
                    ),
                TextInput::make('price')
                    ->label('Precio Unitario')
                    ->numeric()
                    ->required()
                    ->prefix('$'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.id')
                    ->label('ID de Pedido')
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product_name')
                    ->label('Nombre del Producto (Manual)')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Precio Unitario')
                    ->money('USD') // Assuming USD, adjust as needed
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }
}
