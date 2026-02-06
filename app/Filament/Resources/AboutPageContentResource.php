<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutPageContentResource\Pages;
use App\Filament\Resources\AboutPageContentResource\RelationManagers;
use App\Models\AboutPageContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;

class AboutPageContentResource extends Resource
{
    protected static ?string $model = AboutPageContent::class;

    protected static ?string $navigationGroup = 'Páginas';
    protected static ?string $navigationLabel = 'Acerca de';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $processImage = function (TemporaryUploadedFile $file, $record, string $field, int $width, int $height): ?string {
            $tempWebpPath = null;
            try {
                $filename = Str::random(8) . ".webp";
                $directory = 'about-page';

                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($file->getRealPath());

                $image->cover($width, $height);

                $tempWebpPath = tempnam(sys_get_temp_dir(), 'webp_') . '.webp';
                $image->toWebp(90)->save($tempWebpPath);

                $storageDisk = \Illuminate\Support\Facades\Storage::disk('public');
                $fullWebpPath = $storageDisk->putFileAs($directory, new \Illuminate\Http\File($tempWebpPath), $filename);

                if ($record && $record->getOriginal($field) && $record->getOriginal($field) !== $fullWebpPath) {
                    $storageDisk->delete($record->getOriginal($field));
                }

                return $fullWebpPath;

            } catch (\Exception $e) {
                \Log::error("Error al procesar imagen para $field: " . $e->getMessage());
                return null;
            } finally {
                if ($tempWebpPath && file_exists($tempWebpPath)) {
                    unlink($tempWebpPath);
                }
            }
        };

        return $form
            ->schema([
                Forms\Components\Section::make('Breadcrumb')
                    ->schema([
                        TextInput::make('breadcrumb_title')
                            ->label('Título del Breadcrumb')
                            ->required()
                            ->tooltip('Título que aparece en la barra de navegación o encabezado de la página "Acerca de".'),
                    ]),
                Forms\Components\Section::make('Imagen de Portada')
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label('Imagen de Portada (Panorámica 3:1)')
                            ->image()
                            ->disk('public')
                            ->directory('about-page')
                            ->imageEditor()
                            ->imageEditorViewportWidth(900)
                            ->imageEditorViewportHeight(300)
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $record) use ($processImage): ?string {
                                return $processImage($file, $record, 'cover_image', 900, 300);
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
                            ->nullable()
                            ->tooltip('Imagen principal que se muestra en la parte superior de la página "Acerca de". Se recomienda una relación de aspecto 3:1.'),
                    ]),
                Forms\Components\Section::make('Primera Sección "Acerca de"')
                    ->schema([
                        TextInput::make('section1_subtitle')
                            ->label('Subtítulo')
                            ->required()
                            ->tooltip('Subtítulo de la primera sección de contenido de la página "Acerca de".'),
                        TextInput::make('section1_title')
                            ->label('Título')
                            ->required()
                            ->tooltip('Título principal de la primera sección de contenido de la página "Acerca de".'),
                        Textarea::make('section1_paragraph')
                            ->label('Párrafo')
                            ->rows(5)
                            ->required()
                            ->tooltip('Contenido del párrafo de la primera sección de la página "Acerca de".'),
                        FileUpload::make('section1_image')
                            ->label('Imagen (Cuadrada 1:1)')
                            ->image()
                            ->disk('public')
                            ->directory('about-page')
                            ->imageEditor()
                            ->imageEditorViewportWidth(1024)
                            ->imageEditorViewportHeight(1024)
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $record) use ($processImage): ?string {
                                return $processImage($file, $record, 'section1_image', 1024, 1024);
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
                            ->nullable()
                            ->tooltip('Imagen que acompaña la primera sección de contenido de la página "Acerca de". Se recomienda una relación de aspecto 1:1.'),
                    ]),
                Forms\Components\Section::make('Segunda Sección "Acerca de"')
                    ->schema([
                        TextInput::make('section2_subtitle')
                            ->label('Subtítulo')
                            ->required()
                            ->tooltip('Subtítulo de la segunda sección de contenido de la página "Acerca de".'),
                        TextInput::make('section2_title')
                            ->label('Título')
                            ->required()
                            ->tooltip('Título principal de la segunda sección de contenido de la página "Acerca de".'),
                        Textarea::make('section2_paragraph')
                            ->label('Párrafo')
                            ->rows(5)
                            ->required()
                            ->tooltip('Contenido del párrafo de la segunda sección de la página "Acerca de".'),
                        FileUpload::make('section2_image')
                            ->label('Imagen (Cuadrada 1:1)')
                            ->image()
                            ->disk('public')
                            ->directory('about-page')
                            ->imageEditor()
                            ->imageEditorViewportWidth(1024)
                            ->imageEditorViewportHeight(1024)
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $record) use ($processImage): ?string {
                                return $processImage($file, $record, 'section2_image', 1024, 1024);
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
                            ->nullable()
                            ->tooltip('Imagen que acompaña la segunda sección de contenido de la página "Acerca de". Se recomienda una relación de aspecto 1:1.'),
                    ]),
                Forms\Components\Section::make('Sección "Más sobre Nosotros"')
                    ->schema([
                        TextInput::make('more_about_heading_title')
                            ->label('Título Principal')
                            ->required()
                            ->tooltip('Título principal de la sección "Más sobre Nosotros".'),
                        Textarea::make('more_about_heading_description')
                            ->label('Descripción Principal')
                            ->rows(3)
                            ->required()
                            ->tooltip('Descripción principal de la sección "Más sobre Nosotros".'),
                        TextInput::make('point1_title')
                            ->label('Punto 1 Título')
                            ->required()
                            ->tooltip('Título del primer punto destacado en la sección "Más sobre Nosotros".'),
                        Textarea::make('point1_description')
                            ->label('Punto 1 Descripción')
                            ->rows(3)
                            ->required()
                            ->tooltip('Descripción del primer punto destacado en la sección "Más sobre Nosotros".'),
                        TextInput::make('point2_title')
                            ->label('Punto 2 Título')
                            ->required()
                            ->tooltip('Título del segundo punto destacado en la sección "Más sobre Nosotros".'),
                        Textarea::make('point2_description')
                            ->label('Punto 2 Descripción')
                            ->rows(3)
                            ->required()
                            ->tooltip('Descripción del segundo punto destacado en la sección "Más sobre Nosotros".'),
                        TextInput::make('point3_title')
                            ->label('Punto 3 Título')
                            ->required()
                            ->tooltip('Título del tercer punto destacado en la sección "Más sobre Nosotros".'),
                        Textarea::make('point3_description')
                            ->label('Punto 3 Descripción')
                            ->rows(3)
                            ->required()
                            ->tooltip('Descripción del tercer punto destacado en la sección "Más sobre Nosotros".'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('breadcrumb_title')->label('Título del Breadcrumb')->searchable(),
                TextColumn::make('section1_title')->label('Título Sección 1')->searchable(),
                TextColumn::make('section2_title')->label('Título Sección 2')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modal(),
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
            'index' => Pages\ListAboutPageContents::route('/'),
        ];
    }
}
