<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use App\Services\GeminiVisionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Ajustes';

    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->label('Clave')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->hintAction(
                        Forms\Components\Actions\Action::make('key_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('La clave única para el ajuste. Este campo no se puede cambiar una vez creado (ej: GEMINI_API_KEY, FACEBOOK_PIXEL_ID).')
                    ),
                Forms\Components\Textarea::make('value')
                    ->label('Valor')
                    ->required()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('value_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Introduce el valor para la clave de ajuste. Para claves de API u otros datos sensibles, asegúrate de que se almacenen de forma segura.')
                    ),

                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('testGeminiApiKey')
                        ->label('🧪 Probar Clave API de Gemini')
                        ->icon('heroicon-o-beaker')
                        ->action(static::testGeminiApiKeyAction(...))
                        ->visible(fn (Forms\Get $get) => $get('key') === 'GEMINI_API_KEY'),
                ])->columnSpanFull(),
            ]);
    }

    public static function testGeminiApiKeyAction(Forms\Get $get, Forms\Set $set): void
    {
        $apiKey = $get('value');
        if (empty($apiKey)) {
            Notification::make()->title('Error')->body('Por favor, introduce una clave API para probar.')->danger()->send();
            return;
        }

        try {
            $geminiService = app(GeminiVisionService::class);
            $geminiService->setApiKey($apiKey);

            $response = $geminiService->testApiKey();

            if ($response) {
                Notification::make()->title('Éxito')->body('La clave API de Gemini es válida.')->success()->send();
            } else {
                Notification::make()->title('Error')->body('La clave API de Gemini no es válida o la API no respondió correctamente. Revisa los logs para más detalles.')->danger()->send();
            }
        } catch (\Exception $e) {
            Notification::make()->title('Error')->body('Error al probar la clave API: ' . $e->getMessage())->danger()->send();
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Clave')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Valor')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn (string $state): ?string => strlen($state) > 50 ? $state : null),
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

    public static function mutateFormDataBeforeSave(array $data): array
    {
        // Logic to handle saving API key securely if needed
        // For now, it will just save to the database as configured
        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
        ];
    }
}
