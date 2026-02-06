<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Publicaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Detalles de la Publicación')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Título')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null)
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('title_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Introduce el título principal de la publicación. Este será el H1 y el título que verán los visitantes.')
                                    ),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('slug_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('El slug es la parte de la URL que identifica a la publicación. Se genera automáticamente a partir del título, pero puedes ajustarlo si es necesario.')
                                    ),

                                Forms\Components\RichEditor::make('content')
                                    ->label('Contenido')
                                    ->required()
                                    ->columnSpanFull()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('content_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Escribe el contenido completo de la publicación aquí. Puedes usar el editor para dar formato al texto, añadir imágenes y más.')
                                    ),

                                Forms\Components\Textarea::make('excerpt')
                                    ->label('Extracto')
                                    ->maxLength(65535)
                                    ->columnSpanFull()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('excerpt_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Escribe un resumen corto y atractivo de la publicación. Este extracto se puede usar en las vistas previas del blog y en las redes sociales.')
                                    ),
                            ])->columns(2),

                        Forms\Components\Section::make('Imagen')
                            ->schema([
                                Forms\Components\FileUpload::make('featured_image')
                                    ->label('Imagen Destacada')
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('featured_image_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Sube una imagen destacada para la publicación. El editor te permitirá recortarla a 999x666 píxeles para asegurar una visualización óptima y se procesará a formato WebP.')
                                    )
                                    ->disk('public') // Ensure disk is specified for saveUploadedFileUsing
                                    ->directory('blog-posts')
                                    ->nullable()
                                    ->imageEditor()
                                    ->imageEditorMode(2)
                                    ->imageEditorViewportWidth(999)
                                    ->imageEditorViewportHeight(666)
                                    ->saveUploadedFileUsing(function (Forms\Components\FileUpload $component, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, $record): ?string {
                                        $tempWebpPath = null;
                                        try {
                                            $filename = Str::random(40) . '.webp';
                                            $directory = $component->getDirectory();

                                            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                                            $image = $manager->read($file->getRealPath());

                                            // Manual cropping to aspect ratio 999:666 (centered)
                                            $originalWidth = $image->width();
                                            $originalHeight = $image->height();
                                            $targetWidth = 999;
                                            $targetHeight = 666;
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

                                            // Resize to exact dimensions 999x666 (upscale or downscale)
                                            $image->resize($targetWidth, $targetHeight);

                                            $tempWebpPath = tempnam(sys_get_temp_dir(), 'webp_') . '.webp';
                                            $image->toWebp(98)->save($tempWebpPath);

                                            $storageDisk = \Illuminate\Support\Facades\Storage::disk('public');
                                            $fullWebpPath = $storageDisk->putFileAs($directory, new \Illuminate\Http\File($tempWebpPath), $filename);

                                            if ($record && $record->getOriginal('featured_image') && $record->getOriginal('featured_image') !== $fullWebpPath) {
                                                $storageDisk->delete($record->getOriginal('featured_image'));
                                            }

                                            return $fullWebpPath;

                                        } catch (\Exception $e) {
                                            \Log::error('Error al procesar imagen destacada de publicación: ' . $e->getMessage());
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
                                    ]),
                            ]),

                        Forms\Components\Section::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Meta Título')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('meta_title_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('El Meta Título es crucial para el SEO. Debe ser conciso (55-60 caracteres) y contener las palabras clave más importantes. Aparecerá en la pestaña del navegador y como título en los resultados de búsqueda.')
                                    ),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Meta Descripción')
                                    ->maxLength(65535)
                                    ->nullable()
                                    ->columnSpanFull()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('meta_description_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('La Meta Descripción es el texto que aparece debajo del título en los resultados de Google. Escribe un resumen atractivo (150-160 caracteres) que incite a los usuarios a hacer clic.')
                                    ),
                                Forms\Components\TextInput::make('meta_keywords')
                                    ->label('Meta Palabras Clave')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('meta_keywords_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Introduce una lista de palabras clave relevantes para la publicación, separadas por comas. Aunque su importancia para Google ha disminuido, todavía pueden ser útiles para otros motores de búsqueda y para la organización interna.')
                                    ),
                            ])->columns(1),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Estado')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'draft' => 'Borrador',
                                        'published' => 'Publicado',
                                        'archived' => 'Archivado',
                                    ])
                                    ->required()
                                    ->default('draft')
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('status_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Controla la visibilidad de la publicación. "Publicado" la hace visible para todos, "Borrador" la oculta y "Archivado" la saca de la lista principal pero mantiene su registro.')
                                    ),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Fecha de Publicación')
                                    ->nullable()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('published_at_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Establece la fecha y hora de publicación. Puedes programar publicaciones para el futuro.')
                                    ),

                                Forms\Components\Select::make('user_id')
                                    ->label('Autor')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->default(auth()->id())
                                    ->searchable()
                                    ->preload()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('user_id_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Selecciona el autor de la publicación. Por defecto, serás tú.')
                                    ),

                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Destacar en Inicio')
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('is_featured_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Marca esta opción para destacar esta publicación en la página de inicio o en otras secciones destacadas del blog.')
                                    )
                                    ->default(false),

                                Forms\Components\Select::make('blog_category_id')
                                    ->label('Categoría de Blog')
                                    ->relationship('blogCategory', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('blog_category_id_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Selecciona la categoría principal para esta publicación. Esto ayuda a los visitantes a encontrar contenido similar.')
                                    ),

                                Forms\Components\Select::make('blogTags')
                                    ->label('Etiquetas de Blog')
                                    ->relationship('blogTags', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('blogTags_hint')
                                            ->label('')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->tooltip('Añade etiquetas para describir los temas específicos de la publicación. Ayuda a los visitantes a encontrar contenido relacionado.')
                                    ),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Imagen')
                    ->square()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'published',
                        'danger' => 'archived',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Autor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('blogCategory.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Fecha de Publicación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacada')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                        'archived' => 'Archivado',
                    ]),
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Autor'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
        ];
    }
}