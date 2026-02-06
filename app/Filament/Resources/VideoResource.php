<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Filament\Resources\VideoResource\RelationManagers;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-film';

    protected static ?string $navigationGroup = 'Home';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Videos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('title_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Introduce un título descriptivo para el video. Este título será visible en la tienda.')
                    ),
                Forms\Components\FileUpload::make('image_path')
                    ->label('Imagen')
                    ->image()
                    ->imageEditor()
                    ->imageEditorMode(2)
                    ->imageEditorViewportWidth(1024)
                    ->imageEditorViewportHeight(421)
                    ->saveUploadedFileUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, \Filament\Forms\Get $get, $record): ?string {
                        $tempWebpPath = null;
                        try {
                            $titleSlug = Str::slug($get('title'));
                            $filename = "{$titleSlug}-" . Str::random(8) . ".webp";
                            $directory = 'video-thumbnails';

                            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                            $image = $manager->read($file->getRealPath());

                            // Manual cropping to aspect ratio 1024:421 (centered)
                            $originalWidth = $image->width();
                            $originalHeight = $image->height();
                            $targetWidth = 1024;
                            $targetHeight = 421;
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

                            // Resize to exact dimensions 1024x421 (upscale or downscale)
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
                            \Log::error('Error al procesar imagen de video: ' . $e->getMessage());
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
                        // No rules here
                    ])
                    ->validationMessages([
                        // No validation messages here
                    ])->hintAction(
                        Forms\Components\Actions\Action::make('image_path_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Sube una imagen de miniatura para el video (opcional). Esta imagen se mostrará antes de que el video se reproduzca. Se recortará y redimensionará a 1024x421 píxeles.')
                    ),
                Forms\Components\TextInput::make('video_url')
                    ->label('URL del Video')
                    ->required()
                    ->url()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('video_url_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Pega la URL completa del video. Compatible con plataformas como YouTube y Vimeo.')
                    ),
                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->required()
                    ->hintAction(
                        Forms\Components\Actions\Action::make('is_active_hint')
                            ->label('')
                            ->icon('heroicon-o-question-mark-circle')
                            ->tooltip('Marca esta opción para que el video sea visible en la tienda. Desmárcala para ocultarlo sin eliminarlo.')
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Imagen'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('video_url')
                    ->label('URL del Video')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
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
                Tables\Actions\EditAction::make()->iconButton()
                    ->after(function ($record) {
                        $oldImagePath = $record->getOriginal('image_path');
                        $newImagePath = $record->image_path;

                        // If image was removed or replaced, delete the old file.
                        if ($oldImagePath && $oldImagePath !== $newImagePath) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldImagePath);
                        }

                        // If a new image exists and its name is not SEO-friendly, rename it.
                        if ($newImagePath && !Str::contains(basename($newImagePath), Str::slug($record->title))) {
                            $titleSlug = Str::slug($record->title);
                            $newFilename = "{$titleSlug}-" . Str::random(8) . ".webp";
                            $renamedPath = "video-thumbnails/{$newFilename}";

                            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($newImagePath)) {
                                \Illuminate\Support\Facades\Storage::disk('public')->move($newImagePath, $renamedPath);
                                $record->image_path = $renamedPath;
                                $record->save();
                            }
                        }
                    }),
                Tables\Actions\DeleteAction::make()->iconButton()
                    ->after(function ($record) {
                        if ($record->image_path) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($record->image_path);
                        }
                    }),
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
            'index' => Pages\ListVideos::route('/'),
        ];
    }
}
