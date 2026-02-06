<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeaturedProductsSectionResource\Pages;
use App\Filament\Resources\FeaturedProductsSectionResource\RelationManagers;
use App\Models\FeaturedProductsSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use App\Models\Product;

class FeaturedProductsSectionResource extends Resource
{
    protected static ?string $model = FeaturedProductsSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?string $navigationLabel = 'Secciones Destacadas';

    protected static ?string $navigationGroup = 'Home';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Configuración de la Sección Principal')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Título Principal')
                                    ->required()
                                    ->maxLength(255)
                                    ->tooltip('Título principal de la sección de productos destacados.'),
                                Forms\Components\TextInput::make('subtitle')
                                    ->label('Subtítulo')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->tooltip('Subtítulo que acompaña al título principal de la sección de productos destacados.'),
                                Forms\Components\TextInput::make('button_text')
                                    ->label('Texto del Botón Principal')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->tooltip('Texto que se mostrará en el botón principal de la sección de productos destacados.'),
                                Forms\Components\TextInput::make('button_url')
                                    ->label('URL del Botón Principal')
                                    ->url()
                                    ->maxLength(255)
                                    ->nullable()
                                    ->tooltip('URL a la que redirigirá el botón principal de la sección de productos destacados.'),
                            ])->columns(2),

                        Forms\Components\Section::make('Sub-Banners')
                            ->description('Define los dos sub-banners de la sección. Cada uno debe tener una imagen, título y enlace.')
                            ->schema([
                                Forms\Components\Repeater::make('sub_banners_data')
                                    ->label('Sub-Banners')
                                    ->minItems(2)
                                    ->maxItems(2)
                                    ->schema([
                                        Forms\Components\FileUpload::make('image_path')
                                            ->label('Imagen del Sub-Banner')
                                            ->disk('public')
                                            ->directory('featured-sub-banners')
                                            ->image()
                                            ->imageEditor()
                                            ->imageEditorMode(2)
                                            ->imageEditorViewportWidth(330)
                                            ->imageEditorViewportHeight(435)
                                            ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, Get $get, $record): ?string {
                                                $tempWebpPath = null;
                                                try {
                                                    $titleSlug = Str::slug($get('title'));
                                                    $filename = "{$titleSlug}-" . Str::random(8) . '.webp';
                                                    $directory = $component->getDirectory();

                                                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                                                    $image = $manager->read($file->getRealPath());

                                                    // Manual cropping to aspect ratio 330:435 (centered)
                                                    $originalWidth = $image->width();
                                                    $originalHeight = $image->height();
                                                    $targetWidth = 330;
                                                    $targetHeight = 435;
                                                    $targetRatio = $targetWidth / $targetHeight;
                                                    $currentRatio = $originalWidth / $originalHeight;

                                                    if ($currentRatio > $targetRatio) {
                                                        $newWidth = $originalHeight * $targetRatio;
                                                        $image->crop((int) $newWidth, (int) $originalHeight, (int) (($originalWidth - $newWidth) / 2), 0);
                                                    } elseif ($currentRatio < $targetRatio) {
                                                        $newHeight = $originalWidth / $targetRatio;
                                                        $image->crop((int) $originalWidth, (int) $newHeight, 0, (int) (($originalHeight - $newHeight) / 2));
                                                    }

                                                    $image->resize($targetWidth, $targetHeight);

                                                    $tempWebpPath = tempnam(sys_get_temp_dir(), 'webp_') . '.webp';
                                                    $image->toWebp(98)->save($tempWebpPath);

                                                    $storageDisk = \Illuminate\Support\Facades\Storage::disk('public');
                                                    $fullWebpPath = $storageDisk->putFileAs($directory, new \Illuminate\Http\File($tempWebpPath), $filename);

                                                    if ($record && $record->getOriginal('image_path') && $record->getOriginal('image_path') !== $fullWebpPath) {
                                                        $storageDisk->delete($record->getOriginal('image_path'));
                                                    }

                                                    return $fullWebpPath;

                                                } catch (\Exception $e) {
                                                    \Log::error('Error al procesar imagen de sub-banner destacado: ' . $e->getMessage());
                                                    \Filament\Notifications\Notification::make()
                                                        ->title('Error al procesar la imagen')
                                                        ->body('No se pudo guardar la imagen. Por favor, inténtalo de nuevo. Detalles: ' . $e->getMessage())
                                                        ->danger()
                                                        ->send();
                                                    return null;
                                                } finally {
                                                    if ($tempWebpPath && file_exists($tempWebpPath)) {
                                                        unlink($tempWebpPath);
                                                    }
                                                }
                                            })
                                            ->rules([
                                                'image',
                                                'mimes:jpeg,png,jpg,gif,webp',
                                                'max:2048',
                                            ])
                                            ->validationMessages([
                                                'image' => 'El archivo debe ser una imagen.',
                                                'mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif, webp.',
                                                'max' => 'La imagen no debe ser mayor de 2MB.',
                                            ])
                                            ->hintAction(
                                                \Filament\Forms\Components\Actions\Action::make('image_path_hint')
                                                    ->label('')
                                                    ->icon('heroicon-o-question-mark-circle')
                                                    ->tooltip('Sube la imagen para este sub-banner destacado. El editor te permitirá recortarla a 330x435 píxeles para asegurar una visualización óptima y se procesará a formato WebP.')
                                            )
                                            ->required(),
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título del Sub-Banner')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('button_text')
                                            ->label('Texto del Botón')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('button_url')
                                            ->label('URL del Botón')
                                            ->url()
                                            ->required()
                                            ->maxLength(255),
                                    ])->columns(2)
                                    ->defaultItems(2)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                            ]),

                        Forms\Components\Section::make('Grids de Productos')
                            ->description('Define los dos grids de productos. Cada grid debe contener exactamente 3 productos.')
                            ->schema([
                                Forms\Components\Repeater::make('product_grids_data')
                                    ->label('Grids de Productos')
                                    ->minItems(2)
                                    ->maxItems(2)
                                    ->schema([
                                        Forms\Components\Select::make('products')
                                            ->label('Productos')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->minItems(2)
                                            ->required()
                                            ->options(Product::pluck('name', 'id')) // Provide all product options
                                            ->getSearchResultsUsing(fn (string $search) => Product::where('name', 'like', "%{$search}%")->pluck('name', 'id'))
                                            ->getOptionLabelUsing(fn ($value): ?string => Product::find($value)?->name),
                                    ])
                                    ->defaultItems(2)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => 'Grid de Productos ' . (isset($state['products']) ? count($state['products']) : 0) . ' productos'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título Principal')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtitle')
                    ->label('Subtítulo')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('button_text')
                    ->label('Texto del Botón')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modal(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->modal(),
            ])->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function ($records) {
                            foreach ($records as $record) {
                                if (!empty($record->sub_banners_data)) {
                                    foreach ($record->sub_banners_data as $banner) {
                                        if (!empty($banner['image_path'])) {
                                            \Illuminate\Support\Facades\Storage::disk('public')->delete($banner['image_path']);
                                        }
                                    }
                                }
                            }
                        }),
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
            'index' => Pages\ListFeaturedProductsSections::route('/'),
        ];
    }
}