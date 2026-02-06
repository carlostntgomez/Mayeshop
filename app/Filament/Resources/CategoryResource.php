<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use App\Services\GeminiVisionService;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Filament\Resources\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $label = 'Categoría';

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Catálogo';

    protected static ?string $navigationLabel = 'Categorías';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Información de la Categoría')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                                    ->hintAction(
                                        Action::make('name_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Introduce el nombre principal de la categoría. Este será el título que verán los clientes en la tienda y en los listados de productos. Ejemplos: "Vestidos de Noche", "Ropa Casual".')
                                    ),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->maxLength(255)
                                    ->unique(Category::class, 'slug', ignoreRecord: true)
                                    ->hintAction(
                                        Action::make('slug_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('El slug es la parte amigable de la URL que identifica a esta categoría. Se genera automáticamente a partir del nombre, pero puedes ajustarlo manualmente si es necesario para mejorar el SEO o la legibilidad.')
                                    ),
                                
                                Forms\Components\Select::make('product_type_id')
                                    ->relationship('productType', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->nullable()
                                    ->label('Tipo de Producto Asociado')
                                    ->hintAction(
                                        Action::make('product_type_id_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Asocia esta categoría a un tipo de producto principal (ej: "Vestidos", "Conjuntos"). Esto es fundamental para la organización de tu catálogo y para aplicar reglas de negocio específicas. Si no se selecciona, la categoría no se vinculará a un tipo de producto.')
                                    ),
                            ]),
                        Section::make('Imagen')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Imagen para Open Graph (Redes Sociales)')
                                    ->disk('public')
                                    ->directory(config('filesystems.disks.public.category_images_directory'))
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorMode(2)
                                    ->imageEditorViewportWidth(600)
                                    ->imageEditorViewportHeight(315)
                                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, Get $get, $record): ?string {
                                        $tempWebpPath = null;
                                        try {
                                            $nameSlug = Str::slug($get('name'));
                                            $filename = "{$nameSlug}-" . Str::random(8) . ".webp";
                                            $directory = $component->getDirectory();

                                            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                                            $image = $manager->read($file->getRealPath());

                                            // Manual cropping to aspect ratio 600:315 (centered)
                                            $originalWidth = $image->width();
                                            $originalHeight = $image->height();
                                            $targetWidth = 600;
                                            $targetHeight = 315;
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

                                            if ($record && $record->getOriginal('image') && $record->getOriginal('image') !== $fullWebpPath) {
                                                $storageDisk->delete($record->getOriginal('image'));
                                            }

                                            return $fullWebpPath;

                                        } catch (\Exception $e) {
                                            \Log::error('Error al procesar imagen de categoría: ' . $e->getMessage());
                                            Notification::make()
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
                                        Action::make('image_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Sube una imagen representativa para esta categoría. Esta imagen se utilizará en las tarjetas de categoría de la tienda y como imagen de previsualización al compartir en redes sociales (Open Graph). El editor te permitirá recortarla a 600x315 píxeles para asegurar una visualización óptima y se procesará a formato WebP.')
                                    ),
                            ]),
                    ])->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([
                        Section::make('Optimización para Buscadores (SEO)')
                            ->schema([
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('generateSeo')
                                        ->label('✨ Generar SEO con IA')
                                        ->icon('heroicon-o-sparkles')
                                        ->visible(fn (Get $get) => !empty($get('name')))
                                                                        ->action(function (Get $get, Set $set) {
                                                                            $categoryName = $get('name');
                                                                            try {
                                                                                $gemini = app(\App\Services\GeminiVisionService::class);
                                                                                $seoData = $gemini->generateSeoForCategory($categoryName);
                                                                                if (empty($seoData)) {
                                                                                    throw new \Exception('La IA no pudo generar los datos SEO.');
                                                                                }
                                                                                $set('meta_title', $seoData['meta_title'] ?? null);
                                                                                $set('meta_description', $seoData['meta_description'] ?? null);
                                                                                $set('meta_keywords', $seoData['meta_keywords'] ?? null);
                                        
                                                                                Notification::make()->title('¡Contenido SEO generado con IA!')->success()->send();
                                                                            } catch (\Exception $e) {
                                                                                Notification::make()->title('Error de IA')->body($e->getMessage())->danger()->send();
                                                                            }
                                                                        }),
                                ]),
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Título SEO')
                                    ->reactive()
                                    ->hintAction(
                                        Action::make('meta_title_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('El Meta Título es fundamental para el SEO. Debe ser conciso (idealmente entre 50-60 caracteres) y contener las palabras clave más relevantes. Es lo que aparecerá en la pestaña del navegador y como título principal en los resultados de búsqueda de Google.')
                                    ),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Descripción SEO')
                                    ->reactive()
                                    ->hintAction(
                                        Action::make('meta_description_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('La Meta Descripción es el breve resumen que aparece debajo del Meta Título en los resultados de búsqueda. Escribe un texto atractivo (idealmente entre 150-160 caracteres) que motive a los usuarios a hacer clic en tu enlace.')
                                    ),
                                Forms\Components\TextInput::make('meta_keywords')
                                    ->label('Palabras Clave (separadas por coma)')
                                    ->reactive()
                                    ->hintAction(
                                        Action::make('meta_keywords_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Introduce una lista de palabras clave relevantes para esta categoría, separadas por comas. Aunque su impacto directo en el ranking de Google es menor, aún pueden ser útiles para otros motores de búsqueda y para la organización interna de tu contenido.')
                                    ),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }
                                
                                    public static function table(Table $table): Table
                                    {
                                        return $table
                                            ->columns([
                                                ImageColumn::make('image')
                                                    ->label('Imagen')
                                                    ->disk('public')
                                                    ->height(80),
                                                Tables\Columns\TextColumn::make('name')
                                                    ->label('Nombre')
                                                    ->searchable()
                                                    ->sortable(),
                                                Tables\Columns\TextColumn::make('slug')
                                                    ->label('Slug')
                                                    ->searchable()
                                                    ->sortable()
                                                    ->toggleable(isToggledHiddenByDefault: true),
                                                Tables\Columns\TextColumn::make('productType.name')
                                                    ->label('Tipo de Producto')
                                                    ->searchable()
                                                    ->sortable(),
                                            ])
                                            ->filters([
                                                //
                                            ])
                                            ->actions([
                                                EditAction::make()->modalWidth('5xl')->iconButton(),
                                                        ])                                            ->bulkActions([
                                                BulkActionGroup::make([
                                                    DeleteBulkAction::make()
                                                        ->after(function ($records) {
                                                            foreach ($records as $record) {
                                                                if ($record->image) {
                                                                    \Illuminate\Support\Facades\Storage::disk('public')->delete($record->image);
                                                                }
                                                            }
                                                        }),
                                                ]),
                                            ]);
                                    }
                                
                                    public static function getRelations(): array
                                    {
                                        return [
                                            RelationManagers\ProductsRelationManager::class,
                                        ];
                                    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
        ];
    }
}