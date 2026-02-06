<?php
namespace App\Filament\Resources;

use App\Models\Tag;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseTagResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Tag::class;
    
    // Define this in child classes
    abstract public static function getType(): string;

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? Str::of(static::getType())->headline()->append(' Etiquetas');
    }
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Hidden::make('type')
                ->default(static::getType()),
                
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->unique(
                    table: Tag::class,
                    column: 'name',
                    modifyRuleUsing: function (Get $get, ?Model $record) {
                        return function ($rule) use ($get, $record) {
                            $rule->where('type', $get('type') ?? static::getType());
                            
                            if ($record) {
                                $rule->ignore($record->getKey(), $record->getKeyName());
                            }
                            
                            return $rule;
                        };
                    }
                )
                ->hintAction(
                    Action::make('name_hint')
                        ->label('')
                        ->icon('heroicon-o-question-mark-circle')
                        ->tooltip('Introduce el nombre de la etiqueta. Este nombre será visible para los clientes al filtrar productos y debe ser único dentro de su tipo (ej: Casual, Seda, Verano).')
                ),
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', static::getType());
    }
}
