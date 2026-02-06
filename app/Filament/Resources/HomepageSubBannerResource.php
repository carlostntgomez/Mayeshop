<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomepageSubBannerResource\Pages;
use App\Models\HomepageSubBanner;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Http\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Filament\Notifications\Notification;
use Filament\Forms\Get;

class HomepageSubBannerResource extends Resource
{
    protected static ?string $model = HomepageSubBanner::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Home';
    protected static ?string $navigationLabel = 'Sub-banners';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255)
                    ->hintAction(
                        Forms\Components\Actions\Action::make('title_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Introduce el título principal del sub-banner. Este texto será visible para los usuarios.')
                    ),
                Forms\Components\TextInput::make('subtitle')
                    ->label('Subtítulo')
                    ->maxLength(255)
                    ->hintAction(
                        Forms\Components\Actions\Action::make('subtitle_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Añade un subtítulo o descripción corta para el sub-banner. Este campo es opcional.')
                    ),
                Forms\Components\FileUpload::make('image_path')
                    ->label('Imagen del Sub-banner')
                    ->disk('public')
                    ->directory(config('filesystems.disks.public.homepage_sub_banner_images_directory'))
                    ->required()
                    ->image()
                    ->imageEditor()
                    ->imageEditorMode(2)
                    ->imageEditorViewportWidth(135)
                    ->imageEditorViewportHeight(250)
                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, TemporaryUploadedFile $file, $record, Forms\Get $get): ?string {
                        $tempWebpPath = null;
                        try {
                            $title = $get('title') ?? 'sub-banner';
                            $titleSlug = Str::slug($title);
                            $filename = "{$titleSlug}-" . Str::random(8) . '.webp';
                            $directory = config('filesystems.disks.public.homepage_sub_banner_images_directory');

                            $manager = new ImageManager(new Driver());
                            $image = $manager->read($file->getRealPath());

                            // Manual cropping to aspect ratio 135:250 (centered)
                            $originalWidth = $image->width();
                            $originalHeight = $image->height();
                            $targetWidth = 135;
                            $targetHeight = 250;
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

                            // Resize to exact dimensions 135x250 (upscale or downscale)
                            $image->resize($targetWidth, $targetHeight);

                            $tempWebpPath = tempnam(sys_get_temp_dir(), 'webp_') . '.webp';
                            $image->toWebp(98)->save($tempWebpPath);

                            $storageDisk = \Illuminate\Support\Facades\Storage::disk('public');
                            $fullWebpPath = $storageDisk->putFileAs($directory, new File($tempWebpPath), $filename);

                            if ($record && $record->getOriginal('image_path') && $record->getOriginal('image_path') !== $fullWebpPath) {
                                $storageDisk->delete($record->getOriginal('image_path'));
                            }

                            return $fullWebpPath;

                        } catch (\Exception $e) {
                            \Log::error('Error al procesar imagen de sub-banner de inicio: ' . $e->getMessage());
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
                        Forms\Components\Actions\Action::make('image_path_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Sube la imagen para este sub-banner. El editor te permitirá recortarla a 135x250 píxeles para asegurar una visualización óptima y se procesará a formato WebP.')
                    ),
                Forms\Components\TextInput::make('link_url')
                    ->label('URL del Enlace')
                    ->url()
                    ->required()
                    ->maxLength(255)
                    ->hintAction(
                        Forms\Components\Actions\Action::make('link_url_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Introduce la URL completa a la que los usuarios serán redirigidos al hacer clic en el sub-banner. Asegúrate de que la URL sea válida.')
                    ),
                Forms\Components\TextInput::make('order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->hintAction(
                        Forms\Components\Actions\Action::make('order_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Define el orden en que este sub-banner aparecerá en la sección de sub-banners de la página de inicio. Un número más bajo se mostrará primero.')
                    ),
                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true)
                    ->hintAction(
                        Forms\Components\Actions\Action::make('is_active_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Marca esta opción para que el sub-banner sea visible en la página de inicio. Desmárcala para ocultarlo temporalmente sin eliminarlo.')
                    ),
                Forms\Components\ColorPicker::make('background_color')
                    ->label('Color de Fondo')
                    ->nullable()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('background_color_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Selecciona un color de fondo para el sub-banner. Este color se usará si no hay una imagen o como color complementario.')
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtitle')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('link_url'),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\ColorColumn::make('background_color'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modal(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function ($records) {
                            foreach ($records as $record) {
                                if ($record->image_path) {
                                    \Illuminate\Support\Facades\Storage::disk('public')->delete($record->image_path);
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
            'index' => Pages\ListHomepageSubBanners::route('/'),
        ];
    }
}