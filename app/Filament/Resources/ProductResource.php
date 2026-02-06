<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Tag;
use App\Services\GeminiVisionService;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Color;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $label = 'Producto';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Catálogo';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?int $navigationSort = 1;

    protected static bool $isModal = true;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Group::make()->schema([
                Section::make('Producto')->schema([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->live() // Removed onBlur: true
                                                ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) =>
                                                    $set('slug', Str::slug($state))
                                                ),                    TextInput::make('slug')
                        ->required()
                        ->unique(Product::class, 'slug', ignoreRecord: true)
                        ->live()
                        ->hintAction(
                            Action::make('slug_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('El slug es la parte amigable de la URL que identifica a este producto. Se genera automáticamente a partir del nombre, pero puedes ajustarlo manualmente si es necesario para mejorar el SEO o la legibilidad.')
                        ),
                ])->columns(2),

                Section::make('Descripciones')->schema([
                    Forms\Components\RichEditor::make('short_description')
                        ->label('Descripción Corta')
                        ->columnSpanFull()
                        ->hintAction(
                            Action::make('short_description_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Introduce una descripción corta y concisa del producto. Esta descripción se mostrará en las vistas previas del producto y en los listados.')
                        ),
                    Forms\Components\RichEditor::make('description')
                        ->label('Descripción Larga')
                        ->columnSpanFull()
                        ->hintAction(
                            Action::make('description_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Proporciona una descripción detallada del producto. Puedes usar el editor para dar formato al texto, añadir imágenes y más. Esta descripción aparecerá en la página de detalles del producto.')
                        ),
                ]),

                Section::make('Imágenes')->schema([
                    FileUpload::make('main_image')
                        ->label('Imagen Principal (800x800px)')
                        ->disk('public')
                        ->directory(config('filesystems.disks.public.product_images_directory'))
                        ->image()
                        ->imageEditor()
                        ->imageEditorMode(2)
                        ->imageEditorViewportWidth(1024)
                        ->imageEditorViewportHeight(1024)
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
                        ->saveUploadedFileUsing(function (FileUpload $component, $file, Get $get, $record): ?string {
                            return self::processAndSaveImage(
                                $file, 
                                config('filesystems.disks.public.product_images_directory'), 
                                $get('name') ?: 'product', 
                                'main',
                                1024,
                                1024,
                                $record ? $record->getOriginal('main_image') : null
                            );
                        })
                        ->helperText('Se redimensionará automáticamente a 800x800px y se convertirá a WebP')
                        ->hintAction(
                            Action::make('main_image_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Sube la imagen principal del producto. Se recomienda una imagen de alta calidad. El editor te permitirá recortarla a 800x800 píxeles para asegurar una visualización óptima y se procesará a formato WebP.')
                        ),

                    FileUpload::make('image_gallery')
                        ->label('Galería de Imágenes (506x506px)')
                        ->disk('public')
                        ->directory(config('filesystems.disks.public.product_gallery_images_directory'))
                        ->multiple()
                        ->reorderable()
                        ->imageEditor()
                        ->imageEditorMode(2)
                        ->imageEditorViewportWidth(1024)
                        ->imageEditorViewportHeight(1024)
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
                        ->panelLayout('grid')
                        ->saveUploadedFileUsing(function (FileUpload $component, $file, Get $get): ?string {
                            return self::processAndSaveImage(
                                $file,
                                config('filesystems.disks.public.product_gallery_images_directory'),
                                $get('name') ?: 'product',
                                'gallery',
                                1024,
                                1024
                            );
                        })
                        ->deleteUploadedFileUsing(function ($file) {
                            Storage::disk('public')->delete($file);
                        })
                        ->helperText('Se redimensionarán automáticamente a 506x506px y se convertirán a WebP')
                        ->hintAction(
                            Action::make('image_gallery_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Sube imágenes adicionales para la galería del producto. Estas imágenes se mostrarán en la página de detalles del producto. El editor te permitirá recortarlas a 506x506 píxeles y se procesarán a formato WebP.')
                        ),
                ]),

                Section::make('Precios e Inventario')->schema([
                    TextInput::make('price')
                        ->label('Precio Original')
                        ->numeric()
                        ->prefix('COP')
                        ->required()
                        ->hintAction(
                            Action::make('price_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Introduce el precio original de venta del producto. Este será el precio base antes de cualquier descuento o promoción.')
                        ),
                    TextInput::make('sale_price')
                        ->label('Precio de Oferta')
                        ->numeric()
                        ->prefix('COP')
                        ->hintAction(
                            Action::make('sale_price_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Si el producto está en oferta, introduce el precio de oferta aquí. Este precio se mostrará en lugar del precio original.')
                        ),
                    TextInput::make('sku')
                        ->label('SKU')
                        ->unique(Product::class, 'sku', ignoreRecord: true)
                        ->hintAction(
                            Action::make('sku_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Introduce el SKU (Stock Keeping Unit) del producto. Es un código único para identificar el producto en tu inventario.')
                        ),
                    TextInput::make('stock')
                        ->label('Stock')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->hintAction(
                            Action::make('stock_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Introduce la cantidad de unidades de este producto disponibles en tu inventario.')
                        ),
                    TextInput::make('low_stock_threshold')
                        ->label('Umbral de Bajas Existencias')
                        ->numeric()
                        ->default(3)
                        ->hintAction(
                            Action::make('low_stock_threshold_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Define el número de unidades en stock a partir del cual se considerará que el producto tiene bajas existencias. Recibirás una notificación cuando el stock alcance este umbral.')
                        ),
                ])->columns(2),
            ])->columnSpan(['lg' => 2]),

            Group::make()->schema([
                Section::make('Organización')->schema([
                    Select::make('category_id')
                        ->label('Categoría')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->hintAction(
                            Action::make('category_id_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Selecciona la categoría principal a la que pertenece este producto. Esto ayuda a organizar tu catálogo y a los clientes a encontrar productos.')
                        ),
                    Select::make('product_type_id')
                        ->relationship('productType', 'name')
                        ->required()
                        ->label('Tipo de Producto')
                        ->searchable()
                        ->preload()
                        ->hintAction(
                            Action::make('product_type_id_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Selecciona el tipo de producto al que pertenece este artículo (ej: Vestido, Camisa, Pantalón). Esto es crucial para la organización y filtros.')
                        ),

                ]),

                Section::make('Atributos')->schema([
                    Select::make('colors')
                        ->label('Colores')
                        ->multiple()
                        ->relationship('colors', 'name')
                        ->preload()
                        ->searchable()
                        ->hintAction(
                            Action::make('colors_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Selecciona los colores disponibles para este producto. Los clientes podrán filtrar productos por estos colores.')
                        ),

                    Select::make('occasions')
                        ->label('Ocasiones')
                        ->multiple()
                        ->relationship('occasions', 'name')
                        ->preload()
                        ->searchable()
                        ->hintAction(
                            Action::make('occasions_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Selecciona las ocasiones para las que este producto es adecuado (ej: Fiesta, Casual, Boda). Ayuda a los clientes a encontrar el atuendo perfecto.')
                        ),
                    Select::make('estilos')
                        ->label('Estilos')
                        ->multiple()
                        ->relationship('estilos', 'name', fn (Builder $query) => $query->where('type', 'estilo'))
                        ->preload()
                        ->searchable()
                        ->hintAction(
                            Action::make('estilos_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Selecciona los estilos que describen este producto (ej: Bohemio, Clásico, Moderno). Facilita la búsqueda por preferencias de estilo.')
                        ),
                    Select::make('temporadas')
                        ->label('Temporadas')
                        ->multiple()
                        ->relationship('temporadas', 'name', fn (Builder $query) => $query->where('type', 'temporada'))
                        ->preload()
                        ->searchable()
                        ->hintAction(
                            Action::make('temporadas_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Selecciona las temporadas para las que este producto es ideal (ej: Verano, Invierno, Primavera). Ayuda a los clientes a encontrar ropa de temporada.')
                        ),
                    Select::make('materials')
                        ->label('Materiales')
                        ->multiple()
                        ->relationship('materials', 'name', fn (Builder $query) => $query->where('type', 'material'))
                        ->preload()
                        ->searchable()
                        ->hintAction(
                            Action::make('materials_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Selecciona los materiales de los que está hecho este producto (ej: Algodón, Seda, Lino). Permite a los clientes filtrar por tipo de tejido.')
                        ),
                ]),

                Section::make('Visibilidad y SEO')->schema([
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('generateProductDetails')
                                        ->label('✨ Generar Detalles con IA')
                                        ->icon('heroicon-o-sparkles')
                                        ->visible(fn (Get $get) => !empty($get('main_image')))
                                                                                ->action(function (Get $get, Forms\Set $set, $record) { // Agregado $record
                                                                                    Log::info('--- Depuración de Botón de AI ---');
                                            $mainImage = $get('main_image');
                                                                                        $mainImageFilePath = null;

                                            if (is_string($mainImage)) {
                                                // Check if it's a full URL and extract relative path
                                                if (Str::startsWith($mainImage, Storage::disk('public')->url(''))) {
                                                    $mainImageFilePath = Str::after($mainImage, Storage::disk('public')->url(''));
                                                } else {
                                                    $mainImageFilePath = $mainImage;
                                                }
                                            } elseif (is_array($mainImage) && !empty($mainImage)) {
                                                // If it's an array, it likely contains TemporaryUploadedFile objects
                                                foreach ($mainImage as $key => $value) {
                                                    if ($value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                                        $mainImageFilePath = $value->getRealPath(); // Get the full path to the temporary file
                                                        break;
                                                    }
                                                }
                                            } elseif ($mainImage instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                                $mainImageFilePath = $mainImage->getRealPath(); // Get the full path to the temporary file
                                            }

                                            // Fallback to $record->main_image if $mainImageFilePath is still empty
                                            if (empty($mainImageFilePath) && $record && $record->main_image) {
                                                $mainImageFilePath = $record->main_image;
                                            }


                                            if (empty($mainImageFilePath)) {
                                                Notification::make()->title('Error')->body('Por favor, sube una imagen principal primero.')->danger()->send();
                                                return;
                                            }

                                            // Get all available colors from the database
                                            $availableColors = Color::all()->pluck('name', 'id')->toArray();
                                            $availableColorNames = array_values($availableColors); // Just the names for the AI

                                            try {
                                                $geminiVision = app(\App\Services\GeminiVisionService::class);
                                                $productDetails = $geminiVision->describeProductImage($mainImageFilePath, $availableColorNames); // Pass available colors
                                                if (empty($productDetails)) {
                                                    throw new \Exception('La IA no pudo generar los detalles del producto a partir de la imagen.');
                                                }
                                                $set('name', $productDetails['name'] ?? null);
                                                $set('short_description', $productDetails['short_description'] ?? null);
                                                $set('description', $productDetails['long_description'] ?? null);
                                                $set('seo_title', $productDetails['meta_title'] ?? null);
                                                $set('seo_description', $productDetails['meta_description'] ?? null);
                                                $set('seo_keywords', str_replace(["\r", "\n"], '', $productDetails['seo_keywords'] ?? null));

                                                // Set selected colors
                                                if (isset($productDetails['selected_colors']) && is_array($productDetails['selected_colors'])) {
                                                    $selectedColorIds = [];
                                                    foreach ($productDetails['selected_colors'] as $aiColorName) {
                                                        $colorId = array_search($aiColorName, $availableColors);
                                                        if ($colorId !== false) {
                                                            $selectedColorIds[] = $colorId;
                                                        }
                                                    }
                                                    $set('colors', $selectedColorIds);
                                                }
                                                
                                                Notification::make()->title('¡Detalles del producto generados con IA!')->success()->send();
                                            } catch (\Exception $e) {
                                                Notification::make()->title('Error de IA')->body($e->getMessage())->danger()->send();
                                            }
                                        }),
                                ]),
                    Select::make('status')
                        ->label('Estado')
                        ->options([
                            'draft' => 'Borrador',
                            'published' => 'Publicado',
                            'archived' => 'Archivado'
                        ])
                        ->default('draft')
                        ->required()
                        ->hintAction(
                            Action::make('status_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Define el estado de visibilidad del producto. \'Borrador\' lo mantiene oculto, \'Publicado\' lo hace visible en la tienda y \'Archivado\' lo retira de la venta sin eliminarlo.')
                        ),
                    Forms\Components\Toggle::make('is_featured')
                        ->label('Destacar en la página de inicio')
                        ->hintAction(
                            Action::make('is_featured_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Activa esta opción para destacar el producto en la página de inicio o en secciones especiales de productos destacados.')
                        ),
                    Forms\Components\Toggle::make('is_most_selling')
                        ->label('Marcar como Más Vendido')
                        ->hintAction(
                            Action::make('is_most_selling_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Marca esta opción para identificar el producto como uno de los más vendidos. Puede usarse para mostrarlo en secciones específicas o para aplicar lógica de marketing.')
                        ),
                    TextInput::make('seo_title')
                        ->label('Título SEO')
                        ->hintAction(
                            Action::make('seo_title_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('El Meta Título es fundamental para el SEO. Debe ser conciso (idealmente entre 50-60 caracteres) y contener las palabras clave más relevantes. Es lo que aparecerá en la pestaña del navegador y como título principal en los resultados de búsqueda de Google.')
                        ),
                    Forms\Components\Textarea::make('seo_description')
                        ->label('Descripción SEO')
                        ->rows(3)
                        ->hintAction(
                            Action::make('seo_description_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('La Meta Descripción es el breve resumen que aparece debajo del Meta Título en los resultados de búsqueda. Escribe un texto atractivo (idealmente entre 150-160 caracteres) que motive a los usuarios a hacer clic en tu enlace.')
                        ),
                    TextInput::make('seo_keywords')
                        ->label('Palabras Clave (separadas por coma)')
                        ->hintAction(
                            Action::make('seo_keywords_hint')
                                ->label('')
                                ->icon('heroicon-o-question-mark-circle')
                                ->tooltip('Introduce una lista de palabras clave relevantes para este producto, separadas por comas. Aunque su impacto directo en el ranking de Google es menor, aún pueden ser útiles para otros motores de búsqueda y para la organización interna de tu contenido.')
                        ),
                ]),
            ])->columnSpan(['lg' => 1]),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('main_image')
                    ->label('Imagen')
                    ->square()
                    ->defaultImageUrl(url('/images/placeholder.png')),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'archived' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('cop')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
                    ->color(fn ($record): string => 
                        $record->stock <= $record->low_stock_threshold ? 'danger' : 'success'
                    ),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                        'archived' => 'Archivado',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Categoría'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Product $record) {
                        self::deleteProductImages($record);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                self::deleteProductImages($record);
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
        ];
    }

    /**
     * Convierte JSON string a array antes de cargar en el formulario
     */
    public static function mutateFormDataBeforeFill(array $data): array
    {
        // dd('mutateFormDataBeforeFill called', $data); // Add this line for debugging
        // Log::info('mutateFormDataBeforeFill: Initial image_gallery data', ['data' => $data['image_gallery'] ?? 'N/A']);

        if (isset($data['image_gallery']) && is_array($data['image_gallery'])) {
            // If it's already an array (due to model casting), map paths to URLs
            $data['image_gallery'] = array_map(function ($path) {
                // Log::info('mutateFormDataBeforeFill: Mapped path to URL', ['path' => $path, 'url' => Storage::disk('public')->url($path)]);
                return Storage::disk('public')->url($path);
            }, $data['image_gallery']);
        } elseif (isset($data['image_gallery']) && is_string($data['image_gallery'])) {
            // This case might still be relevant if the casting somehow fails or for specific scenarios
            $decoded = json_decode($data['image_gallery'], true);
            // Log::info('mutateFormDataBeforeFill: Decoded image_gallery from string', ['decoded' => $decoded]);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // Log::error('mutateFormDataBeforeFill: JSON decode error from string', ['error' => json_last_error_msg(), 'json_string' => $data['image_gallery']]);
                $data['image_gallery'] = [];
            } elseif (is_array($decoded)) {
                $data['image_gallery'] = array_map(function ($path) {
                    // Log::info('mutateFormDataBeforeFill: Mapped path to URL from decoded string', ['path' => $path, 'url' => Storage::disk('public')->url($path)]);
                    return Storage::disk('public')->url($path);
                }, $decoded);
            } else {
                $data['image_gallery'] = [];
            }
        } else {
            // Log::info('mutateFormDataBeforeFill: image_gallery not set or not an array/string', ['data' => $data['image_gallery'] ?? 'N/A']);
            $data['image_gallery'] = [];
        }
        // Log::info('mutateFormDataBeforeFill: Final image_gallery for form', ['final_data' => $data['image_gallery'] ?? 'N/A']);
        return $data;
    }

    /**
     * Convierte array a JSON string antes de guardar en la base de datos
     */
    public static function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['image_gallery']) && is_array($data['image_gallery'])) {
            $data['image_gallery'] = json_encode($data['image_gallery']);
        }
        return $data;
    }

    /**
     * Procesa y guarda una imagen con las dimensiones especificadas
     */
    private static function processAndSaveImage(
        $file,
        string $directory,
        string $productName,
        string $type,
        int $width,
        int $height,
        ?string $oldImagePath = null
    ): ?string {
        $tempWebpPath = null;
        
        try {
            $nameSlug = Str::slug($productName);
            $filename = "{$nameSlug}-{$type}-" . Str::random(8) . ".webp";
            
            $manager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $manager->read($file->getRealPath());
            
            // Redimensiona y recorta la imagen al centro
            $image->cover($width, $height);
            
            // Guarda temporalmente como WebP
            $tempWebpPath = tempnam(sys_get_temp_dir(), 'webp_') . '.webp';
            $image->toWebp(98)->save($tempWebpPath);
            
            $storageDisk = Storage::disk('public');
            
            // Guarda la nueva imagen
            $fullWebpPath = $storageDisk->putFileAs(
                $directory,
                new \Illuminate\Http\File($tempWebpPath),
                $filename
            );
            
            // Elimina la imagen anterior si existe
            if ($oldImagePath && $storageDisk->exists($oldImagePath)) {
                $storageDisk->delete($oldImagePath);
            }
            
            return $fullWebpPath;
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al procesar la imagen')
                ->body($e->getMessage())
                ->danger()
                ->send();
            
            return null;
            
        } finally {
            if ($tempWebpPath && file_exists($tempWebpPath)) {
                unlink($tempWebpPath);
            }
        }
    }

    /**
     * Elimina todas las imágenes asociadas a un producto
     */
    private static function deleteProductImages(Product $record): void
    {
        $storageDisk = Storage::disk('public');
        
        // Elimina la imagen principal
        if ($record->main_image && $storageDisk->exists($record->main_image)) {
            $storageDisk->delete($record->main_image);
        }
        
        // Elimina las imágenes de la galería
        $gallery = is_string($record->image_gallery) 
            ? json_decode($record->image_gallery, true) 
            : $record->image_gallery;
            
        if (is_array($gallery) && !empty($gallery)) {
            foreach ($gallery as $image) {
                if ($image && $storageDisk->exists($image)) {
                    $storageDisk->delete($image);
                }
            }
        }
    }
}
