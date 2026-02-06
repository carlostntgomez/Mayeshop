<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Arr;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('banners')->truncate();
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

        $processAndStoreImage = function ($sourceImageName, $title, $directory = 'banners') use ($sampleImagePath) {
            $sourcePath = $sampleImagePath . $sourceImageName;
            $titleSlug = Str::slug($title); // Generate slug from title
            $filename = "{$titleSlug}-" . Str::random(8) . '.webp'; // Use descriptive filename

            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($sourcePath);

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

            $fullWebpPath = Storage::disk('public')->putFileAs($directory, new \Illuminate\Http\File($tempWebpPath), $filename);

            if (file_exists($tempWebpPath)) {
                unlink($tempWebpPath);
            }
            return $fullWebpPath;
        };

        $bannersData = [
            [
                'title' => 'Nueva Colección: \'Aura Nocturna\'',
                'subtitle' => 'Descubre diseños que capturan la esencia de la noche. Elegancia y misterio en cada pieza.',
                'price_text' => 'Desde $250.000 COP',
                'button_text' => 'Explorar Colección',
                'button_url' => '/collections/aura-nocturna',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'Los Más Deseados',
                'subtitle' => 'Nuestros vestidos best-seller que definen el poder y la sofisticación.',
                'price_text' => null,
                'button_text' => 'Ver los Favoritos',
                'button_url' => '/categories/best-sellers',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'Oferta Exclusiva: Piezas Únicas',
                'subtitle' => 'Hasta 40% de descuento en vestidos seleccionados. No dejes pasar la oportunidad.',
                'price_text' => '40% OFF',
                'button_text' => 'Comprar Ahora',
                'button_url' => '/sale',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'title' => 'Vestidos de Noche',
                'subtitle' => 'Lujo y glamour para tus eventos más especiales. Conviértete en el centro de todas las miradas.',
                'price_text' => null,
                'button_text' => 'Descubrir Vestidos de Noche',
                'button_url' => '/categories/vestidos-de-noche',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'title' => 'Hecho en Colombia con Pasión',
                'subtitle' => 'Cada diseño es una obra de arte, creada para la mujer poderosa y moderna.',
                'price_text' => null,
                'button_text' => 'Nuestra Historia',
                'button_url' => '/about-us',
                'is_active' => true,
                'order' => 5,
            ],
        ];

        foreach ($bannersData as $bannerData) {
            $imagePath = $processAndStoreImage(Arr::random($allSampleImages), $bannerData['title'], 'banners'); // Pass title
            Banner::create(array_merge($bannerData, [
                'image_path' => $imagePath,
            ]));
        }
    }
}