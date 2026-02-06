<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryImageResource\Pages;
use App\Filament\Resources\GalleryImageResource\RelationManagers;
use App\Models\GalleryImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GalleryImageResource extends Resource
{
    protected static ?string $model = GalleryImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Home';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Imágenes de Galería';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image_path')
                    ->label('Imagen de Galería')
                    ->image()
                    ->directory('gallery-images')
                    ->disk('public')
                    ->required()
                    ->imageEditor()
                    ->imageEditorMode(2)
                    ->imageEditorViewportWidth(800)
                    ->imageEditorViewportHeight(800)
                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, Get $get, $record): ?string {
                        $tempWebpPath = null;
                        try {
                            $altTextSlug = Str::slug($get('alt_text') ?: 'gallery-image');
                            $filename = "{$altTextSlug}-" . Str::random(8) . ".webp";
                            $directory = $component->getDirectory();

                            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                            $image = $manager->read($file->getRealPath());

                            // Manual cropping to aspect ratio 800:800 (centered)
                            $originalWidth = $image->width();
                            $originalHeight = $image->height();
                            $targetWidth = 800;
                            $targetHeight = 800;
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
                            \Log::error('Error al procesar imagen de galería: ' . $e->getMessage());
                            \Filament\Notifications\Notification::make()
                                ->title('Error al procesar la imagen')
                                ->body('No se pudo guardar la imagen. Por favor, inténtalo de nuevo.')
                                ->danger()
                                ->send();
                            return null;
                        } finally {
                            if (isset($tempWebpPath) && file_exists($tempWebpPath)) {
                                unlink($tempWebpPath);
                            }
                        }
                    })
                    ->rules([
                        \Illuminate\Validation\Rule::dimensions()->minWidth(400)->minHeight(400)->ratio(1),
                    ])
                    ->validationMessages([
                        'required' => 'El campo Imagen de Galería es obligatorio.',
                        'dimensions' => 'La imagen debe tener una relación de aspecto de 1:1 y ser de al menos 400x400 píxeles.',
                    ])
                    ->hintAction(
                        Forms\Components\Actions\Action::make('image_path_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Sube una imagen para la galería principal de la página de inicio. La imagen se procesará a formato WebP y se redimensionará a 800x800px.')
                    ),
                Forms\Components\TextInput::make('alt_text')
                    ->label('Texto Alternativo (SEO)')
                    ->nullable()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('alt_text_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Proporciona un texto alternativo descriptivo para la imagen. Esto es crucial para el SEO y la accesibilidad web.')
                    ),
                Forms\Components\TextInput::make('order')
                    ->label('Orden de Visualización')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->hintAction(
                        Forms\Components\Actions\Action::make('order_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Establece el orden de esta imagen en la galería. Un número más bajo se mostrará primero.')
                    ),
                Forms\Components\Toggle::make('is_active')
                    ->label('Activa')
                    ->hintAction(
                        Forms\Components\Actions\Action::make('is_active_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Marca esta opción para que la imagen sea visible en la galería de la página de inicio. Desmárcala para ocultarla sin eliminarla.')
                    )
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Imagen'),
                Tables\Columns\TextColumn::make('alt_text')
                    ->label('Texto Alternativo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Orden')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth('5xl')->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton()
                    ->after(function ($record) {
                        if ($record->image_path) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($record->image_path);
                        }
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->modalWidth('5xl'),
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
            'index' => Pages\ListGalleryImages::route('/'),
        ];
    }
}
