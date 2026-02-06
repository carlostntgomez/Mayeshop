<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Filament\Resources\BannerResource\RelationManagers;
use App\Models\Banner;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Get;
use Illuminate\Http\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Filament\Forms\Components\Actions\Action;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Banners';

    protected static ?string $navigationGroup = 'Home';

    protected static ?int $navigationSort = 5;

    protected static bool $isModal = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Contenido del Banner')
                    ->description('Define el texto y los enlaces que aparecerán en la diapositiva.')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título Principal')
                            ->required()
                            ->live()
                            ->maxLength(255)
                            ->hintAction(
                                Action::make('title_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Introduce el título principal del banner. Este texto será el más prominente y visible para los usuarios en la diapositiva.')
                            ),
                        TextInput::make('subtitle')
                            ->label('Subtítulo')
                            ->maxLength(255)
                            ->hintAction(
                                Action::make('subtitle_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Añade un subtítulo o eslogan que complemente al título principal del banner. Este campo es opcional y puede usarse para dar más contexto.')
                            ),
                        TextInput::make('price_text')
                            ->label('Texto del Precio')
                            ->placeholder('Ej: Desde $129')
                            ->maxLength(255)
                            ->hintAction(
                                Action::make('price_text_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Usa este campo para mostrar información de precios o una oferta especial (ej: "Desde $99.900", "20% de Descuento"). Este campo es opcional y se mostrará junto al título.')
                            ),
                        TextInput::make('button_text')
                            ->label('Texto del Botón')
                            ->required()
                            ->placeholder('Ej: COMPRAR AHORA')
                            ->maxLength(255)
                            ->hintAction(
                                Action::make('button_text_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Define el texto que aparecerá en el botón de llamada a la acción del banner (ej: "Comprar Ahora", "Ver Colección"). Este texto debe ser claro y conciso.')
                            ),
                        TextInput::make('button_url')
                            ->label('URL del Botón')
                            ->required()
                            ->url()
                            ->maxLength(255)
                            ->hintAction(
                                Action::make('button_url_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Introduce la URL completa a la que los usuarios serán redirigidos al hacer clic en el botón del banner. Asegúrate de que la URL sea válida.')
                            ),
                    ])->columns(2),

                Section::make('Imagen y Estado')
                    ->description('Sube la imagen del banner y define su visibilidad.')
                    ->schema([
                                                                                                                                                FileUpload::make('image_path')
                                                                                                                                                    ->label('Imagen del Banner')
                                                                                                                                                    ->disk('public')
                                                                                                                                                    ->directory(config('filesystems.disks.public.banner_images_directory'))
                                                                                                                                                    ->required()
                                                                                                                                                    ->image()
                                                                                                                                                    ->imageEditor()
                                                                                                                                                    ->imageEditorMode(2)
                                                                                                                                                    ->imageEditorViewportWidth(1024)
                                                                                                                                                    ->imageEditorViewportHeight(768)
                                                                                                                                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, Get $get, $record): ?string {
                                                                                                                                                        $tempWebpPath = null;
                                                                                                                                                        try {
                                                                                                                                                            $titleSlug = Str::slug($get('title'));
                                                                                                                                                            $filename = "{$titleSlug}-" . Str::random(8) . ".webp";
                                                                                                                                                            $directory = config('filesystems.disks.public.banner_images_directory');
                                                                        
                                                                                                                                                            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                                                                                                                                                            $image = $manager->read($file->getRealPath());
                                                                        
                                                                                                                                                            // Manual cropping to aspect ratio 1024:768 (centered)
                                                                                                                                                            $originalWidth = $image->width();
                                                                                                                                                            $originalHeight = $image->height();
                                                                                                                                                            $targetWidth = 1024;
                                                                                                                                                            $targetHeight = 768;
                                                                                                                                                            $targetRatio = $targetWidth / $targetHeight;
                                                                                                                                                            $currentRatio = $originalWidth / $originalHeight;
                                                                        
                                                                                                                                                            if ($currentRatio > $targetRatio) {
                                                                                                                                                                // Image is wider than target aspect ratio, crop width
                                                                                                                                                                $newWidth = $originalHeight * $targetRatio;
                                                                                                                                                                $image->crop((int) $newWidth, (int) $originalHeight, (int) (($originalWidth - $newWidth) / 2), 0);
                                                                                                                                                            } elseif ($currentRatio < $targetRatio) {
                                                                                                                                                                // Image is taller than target aspect ratio, crop height
                                                                                                                                                                $newHeight = $originalWidth / $targetRatio;
                                                                                                                                                                $image->crop((int) $originalWidth, (int) $newHeight, 0, (int) (($originalHeight - $newHeight) / 2));
                                                                                                                                                            }
                                                                        
                                                                                                                                                            // Resize to exact dimensions 1024x768 (upscale or downscale)
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
                                                                                                                                                            \Log::error('Error al procesar imagen de banner: ' . $e->getMessage());
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
                                                                                                                                                    ])                            ->hintAction(
                                Action::make('image_path_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Sube la imagen principal para este banner. Se recomienda una imagen de alta calidad y visualmente atractiva. El editor te permitirá recortarla a 1024x768 píxeles para asegurar una visualización óptima y se procesará a formato WebP.')
                            ),
                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->hintAction(
                                Action::make('is_active_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Marca esta opción para que el banner sea visible y activo en el carrusel de la página de inicio. Desmárcala para ocultarlo temporalmente sin eliminarlo.')
                            ),
                        TextInput::make('order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->hintAction(
                                Action::make('order_hint')
                                    ->label('')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->tooltip('Define el orden en que este banner aparecerá en el carrusel de la página de inicio. Los banners con un número de orden más bajo se mostrarán primero.')
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->square(),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modal()
                    ->after(function ($record) {
                        $oldImagePath = $record->getOriginal('image_path');
                        $newImagePath = $record->image_path;

                        // If image was removed or replaced, delete the old file.
                        if ($oldImagePath && $oldImagePath !== $newImagePath) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldImagePath);
                        }
                    }),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->modal(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBanners::route('/'),
        ];
    }
}
