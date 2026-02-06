<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WhatsAppSubscriptionResource\Pages;
use App\Filament\Resources\WhatsAppSubscriptionResource\RelationManagers;
use App\Models\WhatsAppSubscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WhatsAppSubscriptionResource extends Resource
{
    protected static ?string $model = WhatsAppSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Suscripciones de WhatsApp';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->unique(WhatsAppSubscription::class, 'phone_number', ignoreRecord: true)
                    ->label('Número de Teléfono (WhatsApp)')
                    ->hintAction(
                        Forms\Components\Actions\Action::make('phone_number_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Introduce el número de teléfono del suscriptor, incluyendo el código de país. Este número se usará para enviar notificaciones de WhatsApp.')
                    ),
                Forms\Components\Toggle::make('is_subscribed')
                    ->label('Suscrito')
                    ->required()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('is_subscribed_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Marca esta opción si el usuario ha dado su consentimiento explícito para recibir notificaciones de marketing por WhatsApp. Desmárcala si el usuario ha cancelado su suscripción.')
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->sortable()
                    ->label('Número de Teléfono'),
                Tables\Columns\IconColumn::make('is_subscribed')
                    ->boolean()
                    ->label('Suscrito'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Fecha de Suscripción'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Última Actualización'),
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
            'index' => Pages\ListWhatsAppSubscriptions::route('/'),
        ];
    }
}
