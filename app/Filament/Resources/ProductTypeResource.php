<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductTypeResource\Pages;
use App\Filament\Resources\ProductTypeResource\RelationManagers;
use App\Models\ProductType;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Services\ProductAiService;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;

class ProductTypeResource extends Resource
{
    protected static ?string $model = ProductType::class;

    protected static ?string $label = 'Tipo de Producto';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Catálogo';

    protected static ?string $navigationLabel = 'Tipos de Producto';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Información del Tipo de Producto')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre del Tipo de Producto')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null)
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('name_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Introduce el nombre principal del tipo de producto. Este será el título que verán los clientes en la tienda y se usará para agrupar productos similares (ej: "Vestidos", "Camisas", "Pantalones").'),
                                    ),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug del Tipo de Producto')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->disabled()
                                    ->dehydrated()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('slug_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('El slug es la parte amigable de la URL que identifica a este tipo de producto. Se genera automáticamente a partir del nombre, pero puedes ajustarlo manualmente si es necesario para mejorar el SEO o la legibilidad.'),
                                    ),
                                Forms\Components\Select::make('gender')
                                    ->label('Género')
                                    ->options([
                                        'Hombre' => 'Hombre',
                                        'Mujer' => 'Mujer',
                                        'Unisex' => 'Unisex',
                                    ])
                                    ->default('Unisex')
                                    ->required()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('gender_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Selecciona el género al que se asocia este tipo de producto (Hombre, Mujer, Unisex). Esta información es útil para filtros de búsqueda y para la organización interna del catálogo.'),
                                    ),
                            ]),
                        Section::make('Imagen')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Imagen para Open Graph (Redes Sociales)')
                                    ->disk('public')
                                    ->directory(config('filesystems.disks.public.product_type_images_directory'))
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
                                            \Log::error('Error al procesar imagen de tipo de producto: ' . $e->getMessage());
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
                                        Forms\Components\Actions\Action::make('image_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Sube una imagen representativa para este tipo de producto. Esta imagen se utilizará en las tarjetas de tipo de producto de la tienda y como imagen de previsualización al compartir en redes sociales (Open Graph). El editor te permitirá recortarla a 600x315 píxeles para asegurar una visualización óptima.')
                                    ),
                            ]),
                    ])->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([
                        Forms\Components\Section::make('Optimización para Buscadores (SEO)')
                            ->schema([
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('generateSeo')
                                        ->label('✨ Generar SEO con IA')
                                        ->icon('heroicon-o-sparkles')
                                        ->visible(fn (Get $get) => !empty($get('name')))
                                        ->action(function (Get $get, Set $set) {
                                            $productTypeName = $get('name');
                                            $gender = $get('gender');
                                            
                                            \Illuminate\Support\Facades\Log::info('Generando SEO para Tipo de Producto', [
                                                'productTypeName' => $productTypeName,
                                                'gender' => $gender,
                                            ]);

                                            try {
                                                $gemini = app(ProductAiService::class);
                                                $seoData = $gemini->generateSeoForProductType($productTypeName, $gender);
                                                
                                                \Illuminate\Support\Facades\Log::info('Respuesta de AI para SEO de Tipo de Producto', [
                                                    'seoData' => $seoData,
                                                ]);

                                                if (empty($seoData)) {
                                                    throw new \Exception('La IA no pudo generar los datos SEO.');
                                                }
                                                $set('meta_title', $seoData['meta_title'] ?? null);
                                                $set('meta_description', $seoData['meta_description'] ?? null);
                                                $set('meta_keywords', $seoData['meta_keywords'] ?? null);

                                                Notification::make()->title('¡Contenido SEO generado con IA!')->success()->send();
                                            } catch (\Exception $e) {
                                                Notification::make()->title('Error de IA')->body($e->getMessage())->danger()->send();
                                                \Illuminate\Support\Facades\Log::error('Error al generar SEO para Tipo de Producto', ['error' => $e->getMessage()]);
                                            }
                                        }),
                                ]),
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Título SEO')
                                    ->reactive()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('meta_title_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('El Meta Título es fundamental para el SEO. Debe ser conciso (idealmente entre 50-60 caracteres) y contener las palabras clave más relevantes. Es lo que aparecerá en la pestaña del navegador y como título principal en los resultados de búsqueda de Google.')
                                    ),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Descripción SEO')
                                    ->reactive()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('meta_description_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('La Meta Descripción es el breve resumen que aparece debajo del Meta Título en los resultados de búsqueda. Escribe un texto atractivo (idealmente entre 150-160 caracteres) que motive a los usuarios a hacer clic en tu enlace.')
                                    ),
                                Forms\Components\TextInput::make('meta_keywords')
                                    ->label('Palabras Clave (separadas por coma)')
                                    ->reactive()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('meta_keywords_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Introduce una lista de palabras clave relevantes para este tipo de producto, separadas por comas. Aunque su impacto directo en el ranking de Google es menor, aún pueden ser útiles para otros motores de búsqueda y para la organización interna de tu contenido.')
                                    ),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagen')
                    ->disk('public')
                    ->height(80)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Género')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductTypes::route('/'),
        ];
    }
}
