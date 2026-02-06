<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationLabel = 'Pedidos';
    protected static ?int $navigationSort = 1;

    protected static bool $isModal = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles del Cliente')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('customer_name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->hintAction(
                                        Action::make('customer_name_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Nombre del cliente que realizó el pedido.')
                                    ),
                                TextInput::make('customer_lastname')
                                    ->label('Apellido')
                                    ->required()
                                    ->maxLength(255)
                                    ->hintAction(
                                        Action::make('customer_lastname_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Apellido del cliente que realizó el pedido.')
                                    ),
                            ]),
                        TextInput::make('company_name')
                            ->label('Nombre de la Empresa')
                            ->maxLength(255)
                            ->nullable()
                            ->hintAction(
                                Action::make('company_name_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Nombre de la empresa del cliente, si aplica. Este campo es opcional.')
                                ),
                        TextInput::make('customer_email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->hintAction(
                                Action::make('customer_email_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Dirección de correo electrónico del cliente para comunicaciones sobre el pedido.')
                                ),
                        TextInput::make('customer_phone')
                            ->label('Teléfono')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->hintAction(
                                Action::make('customer_phone_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Número de teléfono del cliente para contacto directo.')
                                ),
                        TextInput::make('customer_address')
                            ->label('Dirección')
                            ->required()
                            ->maxLength(255)
                            ->hintAction(
                                Action::make('customer_address_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Dirección completa de envío del cliente.')
                                ),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('customer_city')
                                    ->label('Ciudad')
                                    ->required()
                                    ->maxLength(255)
                                    ->hintAction(
                                        Action::make('customer_city_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Ciudad de residencia del cliente.')
                                    ),
                                TextInput::make('customer_state')
                                    ->label('Departamento')
                                    ->required()
                                    ->maxLength(255)
                                    ->hintAction(
                                        Action::make('customer_state_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Departamento o estado de residencia del cliente.')
                                    ),
                            ]),
                        TextInput::make('customer_country')
                            ->label('País')
                            ->required()
                            ->maxLength(255)
                            ->hintAction(
                                Action::make('customer_country_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('País de residencia del cliente.')
                                ),
                    ])->columns(1),

                Section::make('Detalles del Pedido')
                    ->schema([
                        Select::make('payment_method')
                            ->label('Método de Pago')
                            ->options([
                                'transferencia_bancaria' => 'Transferencia Bancaria Directa',
                                'contra_entrega' => 'Pago Contra Entrega',
                            ])
                            ->required()
                            ->hintAction(
                                Action::make('payment_method_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Método de pago seleccionado por el cliente para este pedido.')
                                ),
                        TextInput::make('total_amount')
                            ->label('Monto Total')
                            ->numeric()
                            ->readOnly()
                            ->prefix('$')
                            ->hintAction(
                                Action::make('total_amount_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Cantidad total del pedido, incluyendo impuestos y envío.')
                                ),
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'processing' => 'Procesando',
                                'completed' => 'Completado',
                                'cancelled' => 'Cancelado',
                            ])
                            ->required()
                            ->default('pending')
                            ->hintAction(
                                Action::make('status_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Estado actual del pedido. Actualiza este campo para reflejar el progreso del pedido.')
                                ),
                    ])->columns(1),

                Section::make('Items del Pedido')
                    ->schema([
                        // The Repeater for items is removed and will be handled by ItemsRelationManager.
                        // Ensure that the Order model has a `hasMany` relationship called 'items'
                        // and that the `ItemsRelationManager` is registered in `getRelations()`.
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')
                    ->label('Nombre del Cliente')
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->label('Correo Electrónico')
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->label('Monto Total')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Método de Pago')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'transferencia_bancaria' => 'info',
                        'contra_entrega' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha de Pedido')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
        ];
    }
}
