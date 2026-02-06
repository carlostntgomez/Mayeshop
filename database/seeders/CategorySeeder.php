<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\ProductType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Arr;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('categories')->truncate();
        Schema::enableForeignKeyConstraints();

        $sampleImagePath = public_path('storage/muetra de imagenes procucto/');
        $allSampleImages = [
            'Image06.webp', 'Image07.webp', 'Image08.webp', 'Image09.webp', 'Image10.webp',
            'Image11.webp', 'Image12.webp', 'Image13.webp', 'Image14.webp', 'Image15.webp',
            'Image16.webp', 'Image17.webp', 'Image18.webp', 'Image19.webp', 'Image20.webp',
            'Image21.webp', 'Image22.webp', 'Image23.webp', 'Image24.webp', 'Image25.webp',
            'Image26.webp', 'Image27.webp', 'Image28.webp', 'Image29.webp', 'Image30.webp',
            'Image31.webp', 'Image32.webp', 'Image33.webp', 'Image34.webp', 'Image35.webp',
            'Image36.webp', 'Image37.webp', 'Image38.webp', 'Image39.webp', 'Image40.webp',
            'Image41.webp', 'Image42.webp', 'Image43.webp', 'Image44.webp', 'Image45.webp',
            'Image46.webp', 'Image47.webp', 'Image48.webp', 'Image49.webp', 'Image50.webp',
            'Image51.webp', 'Image52.webp', 'Image53.webp', 'Image54.webp', 'Image55.webp',
            'Image56.webp',
        ];

        $processAndStoreImage = function ($sourceImageName, $directory = 'category-images') use ($sampleImagePath) {
            $sourcePath = $sampleImagePath . $sourceImageName;
            $filename = Str::random(40) . '.webp';

            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($sourcePath);

            // Manual cropping to aspect ratio 600:315 (centered)
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            $targetWidth = 600;
            $targetHeight = 315;
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

            // Resize to exact dimensions 600x315 (upscale or downscale)
            $image->resize($targetWidth, $targetHeight);

            $tempWebpPath = tempnam(sys_get_temp_dir(), 'webp_') . '.webp';
            $image->toWebp(98)->save($tempWebpPath);

            $fullWebpPath = Storage::disk('public')->putFileAs($directory, new \Illuminate\Http\File($tempWebpPath), $filename);

            if (file_exists($tempWebpPath)) {
                unlink($tempWebpPath);
            }
            return $fullWebpPath;
        };

        $categoriesData = [
            // Vestidos
            [
                'category_type_name' => 'Vestidos',
                'categories' => [
                    [
                        'name' => 'Vestido Corto',
                        'meta_title' => 'Vestidos Cortos (Mini) | Maye Shop',
                        'meta_description' => 'Descubre nuestra colección de vestidos cortos.',
                        'meta_keywords' => 'vestidos cortos, mini, moda femenina',
                    ],
                    [
                        'name' => 'Vestido Midi',
                        'meta_title' => 'Vestidos Midi | Maye Shop',
                        'meta_description' => 'Descubre nuestra colección de vestidos midi.',
                        'meta_keywords' => 'vestidos midi, moda femenina',
                    ],
                    [
                        'name' => 'Vestido Largo',
                        'meta_title' => 'Vestidos Largos (Maxi) | Maye Shop',
                        'meta_description' => 'Encuentra vestidos largos elegantes en Maye Shop.',
                        'meta_keywords' => 'vestidos largos, maxi, moda femenina',
                    ],
                    [
                        'name' => 'Vestido Asimétrico',
                        'meta_title' => 'Vestidos Asimétricos y Con Abertura | Maye Shop',
                        'meta_description' => 'Vestidos con diseños únicos y atrevidos.',
                        'meta_keywords' => 'vestidos asimétricos, vestidos con abertura, moda femenina',
                    ],
                    [
                        'name' => 'Vestido Escote en V',
                        'meta_title' => 'Vestidos Escote en V | Maye Shop',
                        'meta_description' => 'Vestidos con elegante escote en V.',
                        'meta_keywords' => 'vestidos escote v, moda femenina',
                    ],
                    [
                        'name' => 'Vestido Escote Redondo',
                        'meta_title' => 'Vestidos Escote Redondo y Cuadrado | Maye Shop',
                        'meta_keywords' => 'vestidos escote redondo, vestidos escote cuadrado, moda femenina',
                    ],
                    [
                        'name' => 'Vestido Halter',
                        'meta_title' => 'Vestidos Halter y Cuello Alto | Maye Shop',
                        'meta_description' => 'Vestidos con estilo halter o cuello alto.',
                        'meta_keywords' => 'vestidos halter, vestidos cuello alto, moda femenina',
                    ],
                    [
                        'name' => 'Vestido Strapless',
                        'meta_title' => 'Vestidos Strapless y Palabra de Honor | Maye Shop',
                        'meta_description' => 'Vestidos sin tirantes para un look sofisticado.',
                        'meta_keywords' => 'vestidos strapless, vestidos palabra de honor, moda femenina',
                    ],
                    [
                        'name' => 'Vestido Off Shoulder',
                        'meta_title' => 'Vestidos Off Shoulder y Hombros Descubiertos | Maye Shop',
                        'meta_description' => 'Vestidos que realzan tus hombros.',
                        'meta_keywords' => 'vestidos off shoulder, vestidos hombros descubiertos, moda femenina',
                    ],
                    [
                        'name' => 'Vestido Sin Mangas',
                        'meta_title' => 'Vestidos Sin Mangas | Maye Shop',
                        'meta_description' => 'Vestidos frescos y versátiles sin mangas.',
                        'meta_keywords' => 'vestidos sin mangas, moda femenina',
                    ],
                    [
                        'name' => 'Vestido Manga Corta',
                        'meta_title' => 'Vestidos Manga Corta | Maye Shop',
                        'meta_description' => 'Vestidos con manga corta para cualquier ocasión.',
                        'meta_keywords' => 'vestidos manga corta, moda femenina',
                    ],
                    [
                        'name' => 'Vestido Manga Larga',
                        'meta_title' => 'Vestidos Manga Larga | Maye Shop',
                        'meta_description' => 'Vestidos elegantes con manga larga.',
                        'meta_keywords' => 'vestidos manga larga, moda femenina',
                    ],
                    [
                        'name' => 'Vestido Un Hombro',
                        'meta_title' => 'Vestidos Un Hombro y Off Shoulder | Maye Shop',
                        'meta_description' => 'Vestidos con diseño de un solo hombro o hombros descubiertos.',
                        'meta_keywords' => 'vestidos un hombro, vestidos off shoulder, moda femenina',
                    ],
                    [
                        'name' => 'Vestido Manga Voluminosa',
                        'meta_title' => 'Vestidos Manga con Volumen | Maye Shop',
                        'meta_description' => 'Vestidos con mangas dramáticas y voluminosas.',
                        'meta_keywords' => 'vestidos manga volumen, moda femenina',
                    ],
                ],
            ],
            // Conjuntos (Mujer)
            [
                'category_type_name' => 'Conjuntos (Mujer)',
                'categories' => [
                    [
                        'name' => 'Conjuntos Casuales',
                        'meta_title' => 'Conjuntos Casuales Mujer | Maye Shop',
                        'meta_description' => 'Conjuntos cómodos y con estilo para el día a día.',
                        'meta_keywords' => 'conjuntos casuales, ropa de mujer',
                    ],
                    [
                        'name' => 'Conjuntos Elegantes',
                        'meta_title' => 'Conjuntos Elegantes Mujer | Maye Shop',
                        'meta_description' => 'Conjuntos sofisticados para ocasiones especiales.',
                        'meta_keywords' => 'conjuntos elegantes, ropa de mujer',
                    ],
                    [
                        'name' => 'Conjuntos Deportivos',
                        'meta_title' => 'Conjuntos Deportivos Mujer | Maye Shop',
                        'meta_description' => 'Conjuntos deportivos para mujer con estilo.',
                        'meta_keywords' => 'conjuntos deportivos, ropa deportiva mujer',
                    ],
                ],
            ],
            // Accesorios
            [
                'category_type_name' => 'Accesorios',
                'categories' => [
                    [
                        'name' => 'Bolsos',
                        'meta_title' => 'Bolsos de Lujo | Maye Shop',
                        'meta_description' => 'Descubre nuestra colección de bolsos exclusivos.',
                        'meta_keywords' => 'bolsos, accesorios, lujo',
                    ],
                    [
                        'name' => 'Joyería',
                        'meta_title' => 'Joyería Exclusiva | Maye Shop',
                        'meta_description' => 'Piezas de joyería únicas para complementar tu estilo.',
                        'meta_keywords' => 'joyería, accesorios, lujo',
                    ],
                    [
                        'name' => 'Calzado',
                        'meta_title' => 'Calzado de Diseño | Maye Shop',
                        'meta_description' => 'Encuentra el calzado perfecto para cada ocasión.',
                        'meta_keywords' => 'calzado, zapatos, accesorios',
                    ],
                ],
            ],
            // Conjuntos (Hombre)
            [
                'category_type_name' => 'Conjuntos (Hombre)',
                'categories' => [
                    [
                        'name' => 'Conjuntos Casuales',
                        'meta_title' => 'Conjuntos Casuales Hombre | Maye Shop',
                        'meta_description' => 'Conjuntos cómodos y con estilo para el hombre moderno.',
                        'meta_keywords' => 'conjuntos casuales hombre, ropa de hombre',
                    ],
                    [
                        'name' => 'Conjuntos Formales',
                        'meta_title' => 'Conjuntos Formales Hombre | Maye Shop',
                        'meta_description' => 'Conjuntos elegantes para el hombre que busca distinción.',
                        'meta_keywords' => 'conjuntos formales hombre, ropa de hombre',
                    ],
                ],
            ],
            // Pijamas
            [
                'category_type_name' => 'Pijamas',
                'categories' => [
                    [
                        'name' => 'Pijamas Cortas',
                        'meta_title' => 'Pijamas Cortas Mujer | Maye Shop',
                        'meta_description' => 'Pijamas frescas y cómodas para mujer.',
                        'meta_keywords' => 'pijamas cortas, ropa de dormir mujer',
                    ],
                    [
                        'name' => 'Pijamas Largas',
                        'meta_title' => 'Pijamas Largas Mujer | Maye Shop',
                        'meta_description' => 'Pijamas elegantes y confortables para mujer.',
                        'meta_keywords' => 'pijamas largas, ropa de dormir mujer',
                    ],
                ],
            ],
        ];

        foreach ($categoriesData as $categoryTypeCategoryData) {
            $categoryType = ProductType::where('name', $categoryTypeCategoryData['category_type_name'])->first();

            if ($categoryType) {
                foreach ($categoryTypeCategoryData['categories'] as $categoryData) {
                    $imagePath = $processAndStoreImage(Arr::random($allSampleImages), 'category-images');
                    $slug = Str::slug($categoryData['name']);
                    // Check if a category with this slug already exists for a different category type
                    $existingCategory = Category::where('slug', $slug)
                                                ->where('category_type_id', '!=', $categoryType->id)
                                                ->first();

                    if ($existingCategory) {
                        // If a duplicate slug exists for a different category type, append the category type name to the slug
                        $slug = Str::slug($categoryData['name'] . ' ' . $categoryTypeCategoryData['category_type_name']);
                    }

                    Category::create(array_merge($categoryData, [
                        'slug' => $slug,
                        'category_type_id' => $categoryType->id,
                        'image' => $imagePath,
                    ]));
                }
            }
        }
    }
}